from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
from pyspark.ml.feature import CountVectorizer
# IMPORT OTHER MODULES HERE

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
    context.registerFunction("cleantext", clean, ArrayType(StringType()))


    # TASK 5
    added_ngrams = context.sql("select Input_id, labeldem, labelgop, labeldjt, sanitize(body) as body from joined_comments")
    
    # TASK 6A
    cv = CountVectorizer(inputCol="body", outputCol="features", minDF=5, binary=True)
    model = cv.fit(joined_comments)
    result = model.transform(joined_comments)
    result.show()


if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    main(sqlContext)
