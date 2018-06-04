from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext

# IMPORT OTHER MODULES HERE

def main(context):
    """Main function takes a Spark SQL context."""
    # YOUR CODE HERE
    # YOU MAY ADD OTHER FUNCTIONS AS NEEDED
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

if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    main(sqlContext)

