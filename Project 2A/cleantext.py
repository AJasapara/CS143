#!/usr/bin/env python

"""Clean comment text for easier parsing."""

from __future__ import print_function

import re
import string
import argparse
import json
import sys
import bz2


__author__ = ""
__email__ = ""

# Some useful data.
_CONTRACTIONS = {
    "tis": "'tis",
    "aint": "ain't",
    "amnt": "amn't",
    "arent": "aren't",
    "cant": "can't",
    "couldve": "could've",
    "couldnt": "couldn't",
    "didnt": "didn't",
    "doesnt": "doesn't",
    "dont": "don't",
    "hadnt": "hadn't",
    "hasnt": "hasn't",
    "havent": "haven't",
    "hed": "he'd",
    "hell": "he'll",
    "hes": "he's",
    "howd": "how'd",
    "howll": "how'll",
    "hows": "how's",
    "id": "i'd",
    "ill": "i'll",
    "im": "i'm",
    "ive": "i've",
    "isnt": "isn't",
    "itd": "it'd",
    "itll": "it'll",
    "its": "it's",
    "mightnt": "mightn't",
    "mightve": "might've",
    "mustnt": "mustn't",
    "mustve": "must've",
    "neednt": "needn't",
    "oclock": "o'clock",
    "ol": "'ol",
    "oughtnt": "oughtn't",
    "shant": "shan't",
    "shed": "she'd",
    "shell": "she'll",
    "shes": "she's",
    "shouldve": "should've",
    "shouldnt": "shouldn't",
    "somebodys": "somebody's",
    "someones": "someone's",
    "somethings": "something's",
    "thatll": "that'll",
    "thats": "that's",
    "thatd": "that'd",
    "thered": "there'd",
    "therere": "there're",
    "theres": "there's",
    "theyd": "they'd",
    "theyll": "they'll",
    "theyre": "they're",
    "theyve": "they've",
    "wasnt": "wasn't",
    "wed": "we'd",
    "wedve": "wed've",
    "well": "we'll",
    "were": "we're",
    "weve": "we've",
    "werent": "weren't",
    "whatd": "what'd",
    "whatll": "what'll",
    "whatre": "what're",
    "whats": "what's",
    "whatve": "what've",
    "whens": "when's",
    "whered": "where'd",
    "wheres": "where's",
    "whereve": "where've",
    "whod": "who'd",
    "whodve": "whod've",
    "wholl": "who'll",
    "whore": "who're",
    "whos": "who's",
    "whove": "who've",
    "whyd": "why'd",
    "whyre": "why're",
    "whys": "why's",
    "wont": "won't",
    "wouldve": "would've",
    "wouldnt": "wouldn't",
    "yall": "y'all",
    "youd": "you'd",
    "youll": "you'll",
    "youre": "you're",
    "youve": "you've"
}

# You may need to write regular expressions.

def sanitize(text):
    """Do parse the text in variable "text" according to the spec, and return
    a LIST containing FOUR strings
    1. The parsed text.
    2. The unigrams
    3. The bigrams
    4. The trigrams
    """

    # YOUR CODE GOES BELOW:

    # replace tabs/newlines with space
    text = re.sub(r'\s+', ' ', text)

    # remove URLs
    text = re.sub(r'[\(]?http\S+[\)]?|\]\(.*\)', '', text, re.UNICODE)

    # split external punctuations and remove the ones we don't want
    # preserves contractions, percentages, and money amounts
    text = re.findall(r"\$\d+(?:\,\d+)?|\d+\.\d+|[\w'\-%]+|[.,!?;]", text, re.UNICODE)

    # make everything lowercase
    text = [token.lower() for token in text]

    parsed_text, unigrams, bigrams, trigrams = '', '', '', ''

    punc = string.punctuation
    len_text = len(text)

    for i in range(len_text):
        parsed_text += text[i]
        # don't wanna add space at the end
        if i != len_text - 1:
            parsed_text += ' '
        if text[i] not in punc:
            unigrams += text[i]
            # catch case where comment ends with a punctuation
            if i != len_text - 2:
                unigrams += ' '

    for i in range(len_text - 1):
        if text[i] not in punc and text[i + 1] not in punc:
            bigrams += text[i] + '_' + text[i + 1]
            # catch case where comment ends with a punctuation
            if i != len_text - 3:
                bigrams += ' '

    for i in range(len_text - 2):
        if text[i] not in punc and text[i + 1] not in punc and text[i + 2] not in punc:
            trigrams += text[i] + '_' + text[i + 1] + '_' + text[i + 2]
            # catch case where comment ends with a punctuation, might be buggy though
            if i != len_text - 4:
                trigrams += ' '

    return [parsed_text, unigrams, bigrams, trigrams]


if __name__ == "__main__":
    # This is the Python main function.
    # You should be able to run
    # python cleantext.py <filename>
    # and this "main" function will open the file,
    # read it line by line, extract the proper value from the JSON,
    # pass to "sanitize" and print the result as a list.

    # YOUR CODE GOES BELOW.
    file_name = sys.argv[1]

    regex = re.compile(r'^.*[.](?P<ext>\w+)$')
    file_ext = regex.match(file_name).group('ext')

    if file_ext == 'json':
        with open(file_name, "r") as json_data:
            for line in json_data:
                data = json.loads(line)
                print(sanitize(data['body']))
    elif file_ext == 'bz2':
        bz2_file = bz2.BZ2File(file_name)
        line = bz2_file.readline().decode('utf-8')
        while line:
            data = json.loads(line)
            print(sanitize(data['body']))
            line = bz2_file.readline().decode('utf-8')
    else:
        print('invalid file type')
        exit(1)
