# OMiner
OMiner is an opinion minning system, based on my master thesis. The intention is to grow this baby up, and enrich with the help of others. Thanks to take the time to try it.

It's mainly written in PHP but I included some pieces of code of other languages to make it more powerful.

I'm sure you'll find this useful, either as a base for some other system or simply as a teaching aid.

As OpenSource foundaction says, keep author's name and license please, it helps me to grow my ego ;)

Do not hesitate to send me an email with your comments, modules, addons, complaints, etcetera.

jgallegos_acosta@hotmail.com


1. Quick Start.

OMiner covers the basic steps of a system like this. But, as I entirely wrote from scratch, it has particular steps, which I will explain as good as I can.

If you found this project, it means you already know what an opinion mining system is, but if you came to this repository due to the destiny, let me tell you what the h** this stuff is.

An opinion mining system is a program which helps you to know if a written opinion is Positive or Negative.

Yeah! What a surprise!?

It is built following a very well stablished direction, based on the work of Turney et al.
So, the modules cover these steps:

a) Extraction of the opinions (from the web).

b) Parsing the opinons to get tokens .

c) Part-of-Speech tagging of the tokens (we'll explain this later).

d) Filtering the tokens using morpho-sintactic rules.

e*) Defining a "wordbag" (this will be explained later, sorry).

f) Getting the hits retrieved by Google using the "wordbag" and the filtered words.

g) Calculating the semantic orientation using the google hits.

h) Evaluating the system.

As this is based on Turney work, it's built upon a unsupervised method.

---
Greets... JG
