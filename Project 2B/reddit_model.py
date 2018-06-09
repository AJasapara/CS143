from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
from pyspark.ml.feature import CountVectorizer
from pyspark.sql.functions import udf
from pyspark.sql.types import ArrayType, StringType, BooleanType, DoubleType

# Bunch of imports (may need more)
from pyspark.ml.classification import LogisticRegression
from pyspark.ml.tuning import CrossValidator, ParamGridBuilder, CrossValidatorModel
from pyspark.ml.evaluation import BinaryClassificationEvaluator

import os
import cleantext
# IMPORT OTHER MODULES HERE

script_path = os.path.dirname(__file__)

def clean(body):
    parsed_text, unigrams, bigrams, trigrams = cleantext.sanitize(body)
    res = unigrams.split(" ");
    res.extend(bigrams.split(" "))
    res.extend(trigrams.split(" "))
    return res

def main(context):
    """Main function takes a Spark SQL context."""
    # YOUR CODE HERE
    # YOU MAY ADD OTHER FUNCTIONS AS NEEDED



    # TASK 1
    try:
        comments = context.read.parquet("comments.parquet").sample(False, 0.2 , None)
    except:
        comments = context.read.json("comments-minimal.json.bz2")
        comments.write.parquet("comments.parquet")
    try:
        submissions = context.read.parquet("submissions.parquet").sample(False, 0.2 , None)
    except:
        submissions = context.read.json("submissions.json.bz2")
        submissions.write.parquet("submissions.parquet")
    try:
        labels = context.read.parquet("labels.parquet").sample(False, 0.2 , None)
    except:
        labels = context.read.format('csv').options(header='true', inferSchema='true').load("labeled_data.csv")
        labels.write.parquet("labels.parquet")

    try: 
        predicted = context.read.parquet("predicted.parquet")


    except:
        # TASK 2
        comments.createOrReplaceTempView("comments")
        labels.createOrReplaceTempView("labels")
        joined_comments = context.sql("select labels.Input_id, labels.labeldem, labels.labelgop, labels.labeldjt, body from comments join labels on id=Input_id")

        # TASK 4
        joined_comments.createOrReplaceTempView("joined_comments")
        context.registerFunction("sanitize", clean, ArrayType(StringType()))


        # TASK 5
        added_ngrams = context.sql("select Input_id, labeldem, labelgop, labeldjt, sanitize(body) as body from joined_comments")
        #do we wanna do a parquet for ^
        
        # TASK 6A
        cv = CountVectorizer(inputCol="body", outputCol="features", minDF=5, binary=True)
        model = cv.fit(added_ngrams)
        result = model.transform(added_ngrams)
        result.show()

        # # TASK 6B
        result.createOrReplaceTempView("result")

        pos_df = context.sql("select *, if(labeldjt = 1,1,0) as label from result")
        neg_df = context.sql("select *, if(labeldjt = -1,1,0) as label from result")
        pos_df.show()
        neg_df.show()

      

        # TASK 7

        # Initialize two logistic regression models.
        # Replace labelCol with the column containing the label, and featuresCol with the column containing the features.
        poslr = LogisticRegression(labelCol="label", featuresCol="features", maxIter=10).setThreshold(0.2)
        neglr = LogisticRegression(labelCol="label", featuresCol="features", maxIter=10).setThreshold(0.25)
        # This is a binary classifier so we need an evaluator that knows how to deal with binary classifiers.
        posEvaluator = BinaryClassificationEvaluator()
        negEvaluator = BinaryClassificationEvaluator()
        # There are a few parameters associated with logistic regression. We do not know what they are a priori.
        # We will assume the parameter is 1.0. Grid search takes forever.
        # We do a grid search to find the best parameters. We can replace [1.0] with a list of values to try.
        posParamGrid = ParamGridBuilder().addGrid(poslr.regParam, [1.0]).build()
        negParamGrid = ParamGridBuilder().addGrid(neglr.regParam, [1.0]).build()
        # We initialize a 5 fold cross-validation pipeline.
        posCrossval = CrossValidator(
            estimator=poslr,
            evaluator=posEvaluator,
            estimatorParamMaps=posParamGrid,
            numFolds=5)
        negCrossval = CrossValidator(
            estimator=neglr,
            evaluator=negEvaluator,
            estimatorParamMaps=negParamGrid,
            numFolds=5)
        # Although crossvalidation creates its own train/test sets for
        # tuning, we still need a labeled test set, because it is not
        # accessible from the crossvalidator (argh!)
        # Split the data 50/50
        posTrain, posTest = pos_df.randomSplit([0.5, 0.5])
        negTrain, negTest = neg_df.randomSplit([0.5, 0.5])
        # Train the models
        print("Training positive classifier...")
        posModel = posCrossval.fit(posTrain)
        print("Training negative classifier...")
        negModel = negCrossval.fit(negTrain)

        # Once we train the models, we don't want to do it again. We can save the models and load them again later.
        posModel.save("www/pos.model1")
        negModel.save("www/neg.model1")


        # TASK 8
        # joined_comments = context.sql("select labels.Input_id, labels.labeldem, labels.labelgop, labels.labeldjt, body from comments join labels on id=Input_id")
        #wtf is this and where is it from

        # 1. The timestamp when the comment was created.
        # 2. The title of the submission (post) that the comment was made on. 
        # 3. The state that the commenter is from.

        # TASK 9 
        # (1) remove any comments that contain "/s" (denoting that the comment is sarcastic... machine learning does not deal with sarcasm well, and (2) remove any comments that start with &gt; this means the comment contains a quote of another comment and likely contains multiple sentiments not just one. 
        submissions.createOrReplaceTempView("submissions")
        comments.createOrReplaceTempView("comments")
        join_df = context.sql("select comments.link_id as id, comments.body, comments.created_utc, submissions.title, comments.author_flair_text, submissions.score as submission_score, comments.score as comments_score from comments join submissions on replace(comments.link_id, 't3_','')=submissions.id and comments.body not like '%/s%' and comments.body not like '&gt%'")
        join_df.show()
        # # check this later lmao

        # # ON THE UNSEEN DATA XDXD

        # TASK 4
        join_df.createOrReplaceTempView("join_df")
        context.registerFunction("sanitize", clean, ArrayType(StringType()))


        # TASK 5
        new_added_ngrams = context.sql("select id, created_utc, title, author_flair_text, submission_score, comments_score, sanitize(body) as body from join_df")
        # new_added_ngrams = context.sql("select id, created_utc, title, author_flair_text, sanitize(body) as body from join_df")
        #do we wanna do a parquet for ^
        
        # TASK 6A
        # cv = CountVectorizer(inputCol="body", outputCol="features", minDF=5, binary=True)
        # model = cv.fit(new_added_ngrams)
        new_result = model.transform(new_added_ngrams)
        new_result.show()

        loadedPosModel = CrossValidatorModel.load("www/pos.model1")
        loadedNegModel = CrossValidatorModel.load("www/neg.model1")
        posResult = loadedPosModel.transform(new_result).selectExpr("features", "id", "created_utc", "title", "author_flair_text", "body", "submission_score", "comments_score", "probability as pos_probability", "prediction as pos_label", "rawPrediction as pos_raw")
        # posResult = loadedPosModel.transform(new_result)
        # .selectExpr("features", "id", "created_utc", "title", "author_flair_text", "body", "probability as pos_probability", "prediction as pos_label", "rawPrediction as pos_raw")
        posResult.show()
        posNegResult = loadedNegModel.transform(posResult).selectExpr("features", "id", "created_utc", "title", "author_flair_text", "body", "submission_score", "comments_score", "pos_probability", "pos_label", "pos_raw", "probability as neg_probability", "prediction as neg_label", "rawPrediction as neg_raw")
        posNegResult.show()
        posNegResult.createOrReplaceTempView("posNegResult")
        predicted = context.sql("select id, title, created_utc, author_flair_text, submission_score, comments_score, pos_label, neg_label from posNegResult")
        predicted.show()

       
        predicted.write.parquet("predicted.parquet")

   


    # # #maybe write parquet

    # # #can someone switch up some variable names, also with the as in the sql statements as well

    predicted.createOrReplaceTempView("predicted")
    percent_submission = context.sql(
        "select id, AVG(pos_label), AVG(neg_label) FROM predicted GROUP BY id")
    percent_submission.show()
    
    percent_day = context.sql(
        "SELECT FROM_UNIXTIME(created_utc, '%Y%M%D') AS date, AVG(pos_label) AS positive, AVG(neg_label) AS negative FROM predicted GROUP BY date")
    percent_day.show()

    is_state_udf = udf(is_state, BooleanType())
    percent_state = context.sql(
        "SELECT author_flair_text AS state, AVG(pos_labelabel) AS positive, AVG(neglabel) AS negative FROM predicted WHERE(is_state_udf(state)) GROUP BY state")
    percent_state.show()
    
    percent_comment_score = context.sql(
        "SELECT score AS comment_score, AVG(pos_label) AS positive, AVG(neg_label) AS negative FROM predicted GROUP BY comment_score")
    percent_comment_score.show()

    percent_submission_score = context.sql(
        "SELECT submission_score, AVG(pos_label) AS positive, AVG(neg_label) AS negative FROM predicted GROUP BY submission_score")
    percent_submission_score.show()


    # save as csv
    # predicted.repartition(1).write.format("com.databricks.spark.csv").option("header","true").save("predictions.csv")
    # percent_submission.orderBy("AVG(pos)", ascending=False).limit(10).repartition(1).write.format("com.databricks.spark.csv").option("header","true").save("top_pos_submissions.csv")
    # percent_submission.orderBy("AVG(neg)", ascending=False).limit(10).repartition(1).write.format("com.databricks.spark.csv").option("header","true").save("top_neg_submissions.csv")
    # percent_day.repartition(1).write.format("com.databricks.spark.csv").option("header","true").save("time_data.csv")
    # percent_state.repartition(1).write.format("com.databricks.spark.csv").option("header","true").save("state_data.csv")
    # percent_comment_score.repartition(1).write.format("com.databricks.spark.csv").option("header","true").save("comment_score.csv")
    # percent_submission_score.repartition(1).write.format("com.databricks.spark.csv").option("header","true").save("submission_score.csv")




def make_pos(i):
    if i == 1:
        return True
    else:
        return False

def make_neg(i):
    if i == -1:
        return True
    else:
        return False


if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    main(sqlContext)
