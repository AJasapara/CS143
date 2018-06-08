from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
from pyspark.ml.feature import CountVectorizer
from pyspark.sql.functions import udf
from pyspark.sql.types import ArrayType, StringType


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
    pos_neg_udf = context.sql("select *, \
    	case when labeldjt = 1 then 1 else 0 end as positive, \
    	case when labeldjt = -1 then 1 else 0 end as negative \
    	from result")
    pos_neg_udf.show()


if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    main(sqlContext)
