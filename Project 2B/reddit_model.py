from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
from pyspark.ml.feature import CountVectorizer
from pyspark.sql.functions import udf
from pyspark.sql.types import ArrayType, StringType, BooleanType

# Bunch of imports (may need more)
from pyspark.ml.classification import LogisticRegression
from pyspark.ml.tuning import CrossValidator, ParamGridBuilder
from pyspark.ml.evaluation import BinaryClassificationEvaluator


import cleantext
# IMPORT OTHER MODULES HERE

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
        comments = context.read.parquet("comments.parquet")
    except:
    	comments = context.read.json("comments-minimal.json.bz2")
    	comments.write.parquet("comments.parquet")
    try:
        submissions = context.read.parquet("submissions.parquet")
    except:
    	submissions = context.read.json("submissions.json.bz2")
    	submissions.write.parquet("submissions.parquet")
    try:
        labels = context.read.parquet("labels.parquet")
    except:
    	labels = context.read.format('csv').options(header='true', inferSchema='true').load("labeled_data.csv")
    	labels.write.parquet("labels.parquet")

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

    # TASK 6B
    result.createOrReplaceTempView("result")
    # pos_neg_udf = context.sql("select *, \
    # 	case when labeldjt = 1 then 1 else 0 end as positive, \
    # 	case when labeldjt = -1 then 1 else 0 end as negative \
    # 	from result")
    # pos_neg_udf.show()

    pos_df = context.sql("select *, if(labeldjt = 1,1,0) as label from result")
    neg_df = context.sql("select *, if(labeldjt = -1,1,0) as label from result")
    pos_df.show()
    neg_df.show()

  

    # TASK 7

    # Initialize two logistic regression models.
    # Replace labelCol with the column containing the label, and featuresCol with the column containing the features.
    poslr = LogisticRegression(labelCol="label", featuresCol="features", maxIter=10)
    neglr = LogisticRegression(labelCol="label", featuresCol="features", maxIter=10)
    # This is a binary classifier so we need an evaluator that knows how to deal with binary classifiers.
    posEvaluator = BinaryClassificationEvaluator()
    negEvaluator = BinaryClassificationEvaluator()
    # There are a few parameters associated with logistic regression. We do not know what they are a priori.
    # We do a grid search to find the best parameters. We can replace [1.0] with a list of values to try.
    # We will assume the parameter is 1.0. Grid search takes forever.
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
    posModel.save("www/pos.model")
    negModel.save("www/neg.model")

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
