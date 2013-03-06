PHPhonex
========

PHPhonex is an implementation of the Phonex name matching algorithm, which itself is a combination of the Soundex and Metaphone algorithms. Phonex was discovered by A. J. Lait and B. Randell at the Department of Computing Science, University of Newcastle upon Tyne. See the paper: http://www.cs.utah.edu/contest/2005/NameMatching.pdf

Phonex takes a name (say... Robert), and produces a four character string representing that name's phonetic components, i.e. R130. This can be comared with Rhobbit (R130) or even Robby (R100) to compare names.

It's not perfect by any stretch, but it can give you an extra confidence in matching vs Levenshtein or Soundex/Metaphone when matching English names - as evaluated by this paper: http://www.waset.org/journals/waset/v1/v1-47.pdf

Usage
-----
PHPhonix is a composer-compatible library.