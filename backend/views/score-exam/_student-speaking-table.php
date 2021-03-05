<?php
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$numberArray = [0 => null, 1 => null, 2 => null, 3 => null, 4 => null, 5 => null];
$levels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
$sectionTitlesByLevel = [
    "A1" => ['Interview', 'Find the differences', 'Tell a story'],
    "A2" => ['Interview', 'Q & A', 'Tell a story'],
    "B1" => ['Interview', 'Talk about a picture', 'Candidate interaction'],
    "B2" => ['Interview', 'Compare and contrast', 'Candidate interaction'],
    "C1" => ['Interview', 'Candidate interaction', 'Sustained monologue'],
    "C2" => ['Interview', 'Candidate interaction', 'Sustained monologue'],
];
$fieldsToScoreByLevel = [
    "A1" => [
        ['Socio-linguistic awareness', 'Interaction', 'Speaker understanding', 'Fluency', 'Vocabulary', 'Grammar'],
        ['Task understanding', 'Fluency', 'Vocabulary', 'Grammar'],
        ['Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
    ],
    "A2" => [
        ['Socio-linguistic awareness', 'Speaker understanding', 'Interaction', 'Vocabulary', 'Grammar'],
        ['Task understanding', 'Interaction', 'Vocabulary', 'Grammar', 'Pronunciation'],
        ['Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
    ],
    "B1" => [
        ['Socio-linguistic awareness', 'Interaction', 'Speaker understanding', 'Fluency', 'Vocabulary', 'Grammar'],
        ['Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
        ['Interaction', 'Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
    ],
    "B2" => [
        ['Socio-linguistic awareness', 'Speaker understanding', 'Interaction', 'Fluency', 'Vocabulary', 'Grammar'],
        ['Task understanding', 'Production', 'Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
        ['Interaction', 'Production', 'Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
    ],
    "C1" => [
        ['Socio-linguistic awareness', 'Interaction', 'Speaker understanding', 'Fluency', 'Accuracy', 'Lexical range'],
        ['Interaction', 'Production', 'Fluency', 'Accuracy', 'Lexical range', 'Pronunciation'],
        ['Production', 'Fluency', 'Accuracy', 'Lexical range', 'Pronunciation'],
    ],
    "C2" => [
        ['Socio-linguistic awareness', 'Interaction', 'Production', 'Fluency', 'Accuracy', 'Lexical range', 'Understanding'],
        ['Socio-linguistic awareness', 'Interaction', 'Production', 'Fluency', 'Accuracy', 'Lexical range'],
        ['Socio-linguistic awareness', 'Production', 'Fluency', 'Accuracy', 'Lexical range', 'Pronunciation'],
    ],
];
$tooltipMessageByScore = [
    "A1" => [
        [
            [
                "Insufficient social contact for assessment.",
                "Does not establish social contact in the form of greetings or polite forms.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Uses some, but not all, forms of basic social contact e.g. only greetings but is unaware of the need for polite forms.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can establish basic social contact by using the simplest everyday polite forms of greetings and farewells, introductions, saying please, thank you, sorry etc."
            ],
            [
                "Insufficient language for assessment.",
                "Is unable to answer the majority of basic questions asked.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Is only able to answer some simple questions, but not others. Basic interaction is a challenge.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can interact in a simple way even though communication is totally dependent on repetition, rephrasing and repair. Can answer simple questions.",
            ],
            [
                "Insufficient language for assessment.",
                "Understands the bare minimum of what the examiner is saying.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can only understand some of the everyday expressions asked, despite slow and clear speech. Struggles to follow instructions.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can understand everyday expressions regarding simple needs of a concrete type, delivered in clear, slow and repeated speech. Can understand questions and instructions addressed carefully and slowly to him.",
            ],
            [
                "Insufficient language for assessment.",
                "Is only able to articulate words but not connect them into simple sentences.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can manage a few very short utterances, but struggles to use connectors.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can manage very short, isolated, mainly pre-packaged utterances, with much pausing to search for expressions, to articulate less familiar words, and to repair communication. Is able to use basic connectors such as “and”, “or”, “but”.",
            ],
            [
                "Insufficient language for assessment.",
                "Has very basic knowledge of some words, but does not produce the vocabulary he/she is expected to.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a basic vocabulary repertoire, but is still not familiar with a lot of the vocabulary he/she is expected to produce.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a basic vocabulary repertoire of isolated words and phrases related to particular concrete situations.",
            ],
            [
                "Insufficient language for assessment.",
                "Does not use any grammatical structures correctly.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Is unable to use simple grammatical structures some, but not most, of the time.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Shows only limited control of a few simple grammatical structures and sentence patterns in a learnt repertoire.",
            ]
        ],
        [
            [
                "Insufficient language for assessment.",
                "Cannot understand most of the instructions addressed to him/her.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can understand some of the instructions and questions addressed to him/her but still struggles with others.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can understand questions and instructions addressed carefully and slowly to him/her and follow short, simple directions.",
            ],
            [
                "Insufficient language for assessment.",
                "Is only able to articulate words but not connect them into simple sentences.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can manage a few very short utterances, but struggles to use connectors.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can manage very short, isolated, mainly pre-packaged utterances, with much pausing to search for expressions, to articulate less familiar words, and to repair communication. Is able to use basic connectors such as “and”, “or”, “but”.",
            ],
            [
                "Insufficient language for assessment.",
                "Has very basic knowledge of some words, but does not have the vocabulary he/she is expected to produce.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a basic vocabulary repertoire, but is still not familiar with a lot of the vocabulary he/she is expected to produce.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a basic vocabulary repertoire of isolated words and phrases related to particular concrete situations.",
            ],
            [
                "Insufficient language for assessment.",
                "Does not use any grammatical structures correctly.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Is unable to use simple grammatical structures some, but not most, of the time.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Shows only limited control of a few simple grammatical structures and sentence patterns in a learnt repertoire.",
            ]
        ],
        [
            [
                "Insufficient language for assessment.",
                "Is only able to articulate words but not connect them into simple sentences.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can manage a few very short utterances, but struggles to use connectors.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can manage very short, isolated, mainly pre-packaged utterances, with much pausing to search for expressions, to articulate less familiar words, and to repair communication.",
            ],
            [
                "Insufficient language for assessment.",
                "Has very basic knowledge of some words, but does not have  the vocabulary he/she is expected to produce.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a basic vocabulary repertoire, but is still not familiar with a lot of the vocabulary he/she is expected to produce.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a basic vocabulary repertoire of isolated words and phrases related to particular concrete situations.",
            ],
            [
                "Insufficient language for assessment.",
                "Does not use any grammatical structures correctly.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Is unable to use simple grammatical structures some, but not most of the time.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Shows only limited control of a few simple grammatical structures and sentence patterns in a learnt repertoire.",
            ],
            [
                "Insufficient language for assessment.",
                "Unintelligible, interlocutor is unable to understand any of the language produced.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can be understood some if not most of the time.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Pronunciation of a very limited repertoire of learnt words and phrases can be understood with some effort by native speakers used to dealing with speakers of his / her language group.",
            ],
        ],
    ],
    "A2" => [
        [
            [
                "Candidate performance below score 1.",
                "Struggles with basic social contact, makes it difficult for examiner to interact. Finds it hard to socialise using even the most basic expressions.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can establish some social contact, but struggles to perform well when it comes to basic language functions. Struggles to socialise using the simplest common expressions.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can establish social contact: greetings and farewells; giving thanks. Can perform and respond to basic language functions (e.g. information exchange, requests). Can socialise using the simplest common expressions and following basic routines",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to understand even clear, slow speech most of the time. Relies purely on repetition and reformulation.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can only understand clear, standard speech on familiar matters at times. Relies on repetition a lot. Finds it difficult to have even simple every day conversations.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can understand enough to manage simple, routine exchanges. Can generally understand clear, standard speech on familiar matters, if he/she can ask for repetition or reformulation. Can understand what is said clearly and slowly in simple every day conversation.",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to interact in structured situations and short conversations. Isn’t particularly able to ask and answer questions related to predictable everyday situations. Does not handle very short social exchanges well and does not understand enough to keep the conversation going.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Struggles to interact with reasonable ease in structured situations and short conversations. Can ask and answer some questions related to predictable everyday situations. Struggles to handle very short social exchanges but and does not understand enough to keep the conversation going.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can interact with reasonable ease in structured situations and short conversations, with some help. Can ask and answer questions and exchange information on familiar topics in predictable everyday situations. Can handle very short social exchanges but is rarely able to understand enough to keep the conversation going.",
            ],
            [
                "Candidate performance below score 1.",
                "Has a very basic repertoire that is not sufficient to help him / her deal with everyday situations. He/she struggles a lot with language production.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a basic repertoire of language that allows him/her to deal with some everyday situations, although he/she still struggles and has to search for words a lot of the time. Can produce some brief expressions in order to deal with simple needs, but struggles to do so.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a repertoire of basic language which enables him/her to deal with everyday situations with predictable content, though he/she will generally have to search for words. Can produce brief everyday expressions in order to satisfy simple needs of a concrete type: personal details, daily routines, wants and needs, requests for information.",
            ],
            [
                "Candidate performance below score 1.",
                "Uses most simple structures incorrectly and makes grammatical errors most of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Uses a lot of simple structures incorrectly. Makes basic mistakes on a constant basis, thus leading to misunderstandings often.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Uses some simple structures correctly but still systematically makes basic mistakes – for example tends to mix up tenses and forgets to mark agreement. Nevertheless, it is usually clear what he/she is trying to say",
            ],
        ],
        [
            [
                "Candidate performance below score 1.",
                "Does not understand task, and further clarification is of no help. Is unable to convey misunderstanding to the interlocutor.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Struggles to understand what he/she is supposed to do, even if the speaker takes the trouble. Finds it difficult to communicate a lack of understanding and lacks the appropriate knowledge to ask for further clarification.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can indicate when he/she is following and can be made to understand what is necessary, if the speaker takes the trouble. Can communicate in simple and routine tasks using simple phrases to ask for and provide things, to get simple information and to discuss what to do next.",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to interact in structured situations and short conversations. Isn’t particularly able to ask and answer questions related to predictable everyday situations. Does not handle very short social exchanges well and does not understand enough to keep the conversation going.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Struggles to interact with reasonable ease in structured situations and short conversations. Can ask and answer some questions related to predictable everyday situations. Struggles to handle very short social exchanges but and does not understand enough to keep the conversation going.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can interact with reasonable ease in structured situations and short conversations, with some help. Can ask and answer questions and exchange information on familiar topics in predictable everyday situations. Can handle very short social exchanges but is rarely able to understand enough to keep the conversation going.",
            ],
            [
                "Candidate performance below score 1.",
                "Has a very basic repertoire that is not sufficient to help him / her deal with everyday situations. He/she struggles a lot with language production.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a basic repertoire of language that allows him/her to deal with some everyday situations, although he/she still struggles and has to search for words a lot of the time. Can produce some brief expressions in order to deal with simple needs, but struggles to do so.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a repertoire of basic language which enables him/her to deal with everyday situations with predictable content, though he/she will generally have to search for words. Can produce brief everyday expressions in order to satisfy simple needs of a concrete type: personal details, daily routines, wants and needs, requests for information.",
            ],
            [
                "Candidate performance below score 1.",
                "Uses most simple structures incorrectly and makes grammatical errors most of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Uses a lot of simple structures incorrectly. Makes basic mistakes on a constant basis, thus leading to misunderstandings often.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Uses some simple structures correctly but still systematically makes basic mistakes – for example tends to mix up tenses and forget to mark agreement nevertheless, it is usually clear what he/she is trying to say",
            ],
            [
                "Candidate performance below score 1.",
                "Pronunciation is not clear enough to be understood most of the time and conversational partners struggle to understand what he/she is saying.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Pronunciation is sometimes clear enough to be understood, with a noticeable foreign accent, and conversational partners will need to ask for repetition from often.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Pronunciation is generally clear enough to be understood despite a noticeable foreign accent, but conversational partners will need to ask for repetition from time to time",
            ]
        ],
        [
            [
                "Candidate performance below score 1.",
                "Is mostly unable to tell a story, regardless of how simply. Is unable to use simple descriptive language. ",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can tell a story simply but struggles to do so. Can give very basic descriptions of events and activities. Struggles with the use of simple descriptive language to make brief statements about and compare objects and possessions.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can tell a story or describe something in a simple list of points. Can give short, basic descriptions of events and activities. Can use simple descriptive language to make brief statements about and compare objects and possessions",
            ],
            [
                "Candidate performance below score 1.",
                "Has a very basic repertoire that is not sufficient to help him / her deal with everyday situations. He/she struggles a lot with language production.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a basic repertoire of language that allows him/her to deal with some everyday situations, although he/she still struggles and has to search for words a lot of the time. Can produce some brief expressions in order to deal with simple needs, but struggles to do so.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a repertoire of basic language which enables him/her to deal with everyday situations with predictable content, though he/she will generally have to search for words. Can produce brief everyday expressions in order to satisfy simple needs of a concrete type: personal details, daily routines, wants and needs, requests for information.",
            ],
            [
                "Candidate performance below score 1.",
                "Is mostly unable to tell a story, regardless of how simply. Is unable to use simple descriptive language.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Uses a lot of simple structures incorrectly. Makes basic mistakes on a constant basis, thus leading to misunderstandings often.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Uses some simple structures correctly but still systematically makes basic mistakes – for example tends to mix up tenses and forget to mark agreement nevertheless, it is usually clear what he/she is trying to say",
            ],
            [
                "Candidate performance below score 1.",
                "Pronunciation is not clear enough to be understood most of the time and conversational partners struggle to understand what he/she is saying.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Pronunciation is sometimes clear enough to be understood, with a noticeable foreign accent, and conversational partners will need to ask for repetition from often.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Pronunciation is generally clear enough to be understood despite a noticeable foreign accent, but conversational partners will need to ask for repetition from time to time",
            ],
        ],
    ],
    "B1" => [
        [
            [
                "Candidate performance below score 1.",
                "Is unable to communicate politely. Is not aware of the salient politeness conventions or of the differences between customs, usages, attitudes, values and beliefs prevalent in the community concerned.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can recognise how to communicate politely at times. Is aware of the salient politeness conventions, although not at all times. Is somewhat aware of the most significant differences between the customs, usages, attitudes, values and beliefs prevalent in the community concerned and those of his or her own.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can recognise how to communicate politely and begin to appreciate customs and social norms in the language. Is aware of the salient politeness conventions and acts appropriately. Is aware of, and looks out for signs of, the most significant differences between the customs, usages, attitudes, values and beliefs prevalent in the community concerned and those of his or her own",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to maintain a level of interaction with native speakers, and with mostly inaccurate use of simple language. Is not able to initiate, maintain and close simple face-to-face conversation, even on topics that are familiar or of personal interest. Does not repeat back part of what someone has said to confirm mutual understanding.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Maintains some level of interaction with native speakers on a variety of themes, with some (although not always) accurate use of simple language. Is able to initiate, maintain and close simple face-to-face conversation on topics that are familiar or of personal interest, although does not do this elegantly. Can repeat back at times part of what someone has said to confirm mutual understanding.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can maintain some level of interaction with native speakers on a variety of themes, mainly through accurate use of simple language. Can initiate, maintain and close simple face-to-face conversation on topics that are familiar or of personal interest. Can repeat back part of what someone has said to confirm mutual understanding.",
            ],
            [
                "Candidate performance below score 1.",
                "Is only able to follow the bare minimum of clearly articulated speech directed at him/her in everyday conversation Asks for repetition of particular words and phrases most of the time. Struggles to understand things that are literal in meaning and has no grasp of abstract language.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can follow some clearly articulated speech directed at him/her in everyday conversation, but will have to ask for repetition of particular words and phrases a lot of the time. Can understand some things that are literal in meaning but has no grasp of abstract language.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can follow clearly articulated speech directed at him/her in everyday conversation, though will sometimes have to ask for repetition of particular words and phrases. Will still struggle with speech that is uttered at a natural or native pace. Can understand most things that are literal in meaning but has very limited grasp of abstract language.",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to use even simple language in order to describe experiences and events, dreams, hopes and ambitions. Is unable to narrate a story or relate the plot of a book or film, even by using very basic language. Cannot give a straightforward description of one of a variety of subjects within his/her field of interest. Does not keep going comprehensibly, and relies on pausing for grammatical and lexical planning most of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can connect only simple phrases in order to describe experiences and events, dreams, hopes and ambitions. Can briefly and at times incorrectly give reasons and explanations. Can narrate a story or relate the plot of a book or film, but only by using very basic language. Can give a straightforward description of one of a variety of subjects within his/her field of interest, presenting it as a linear sequence of points, even though not elegantly. Does not keep going comprehensibly, and relies on pausing for grammatical and lexical planning. Repair is very evident, both in short and longer stretches of free production.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can connect phrases in a simple way in order to describe experiences and events, dreams, hopes and ambitions. Can briefly give reasons and explanations. Can narrate a story or relate the plot of a book or film. Can reasonably fluently sustain a straightforward description of one of a variety of subjects within his/her field of interest, presenting it as a linear sequence of points. Can keep going comprehensibly, even though pausing for grammatical and lexical planning and repair is very evident, especially in longer stretches of free production.",
            ],
            [
                "Candidate performance below score 1.",
                "Does not have enough vocabulary to express him/ herself on topics such as family, hobbies and interests, work, travel, and current events. Shows almost no control of elementary vocabulary and makes major errors most of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has enough vocabulary to express him/ herself on topics such as family, hobbies and interests, work, travel, and current events but uses a lot of hesitation and circumlocutions. Does not have a sufficient range of language to describe unpredictable situations, explain the main points in an idea or problem with reasonable precision and express thoughts on abstract or cultural topics such as music and films. Shows some control of elementary vocabulary but major errors still occur on a regular basis.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has enough language to get by, with sufficient vocabulary to express him/ herself with some hesitation and circumlocutions on topics such as family, hobbies and interests, work, travel, and current events. Has a sufficient range of language to describe unpredictable situations, explain the main points in an idea or problem with reasonable precision and express thoughts on abstract or cultural topics such as music and films. Shows good control of elementary vocabulary but major errors still occur when expressing more complex thoughts or handling unfamiliar topics and situations",
            ],
            [
                "Candidate performance below score 1.",
                "Communicates with very little accuracy in familiar contexts. Has poor grammatical control most of the time. Errors occur often, and it is unclear what he/she is trying to express most of the time. Does not have a repertoire of frequently used “routines” and patterns associated with more predictable situations.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Communicates with some accuracy in familiar contexts; poor grammatical control though with noticeable mother tongue influence. Errors occur, and it is unclear at times what he/she is trying to express. Does not use a repertoire of frequently used “routines” and patterns associated with more predictable situations as accurately as expected at this level.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Communicates with reasonable accuracy in familiar contexts; generally good control though with noticeable mother tongue influence. Errors occur, but it is clear what he/she is trying to express. Uses reasonably accurately a repertoire of frequently used “routines” and patterns associated with more predictable situations.",
            ],
        ],
        [
            [
                "Candidate performance below score 1.",
                "Struggles to use even simple language in order to describe experiences and events, dreams, hopes and ambitions. Is unable to narrate a story or relate the plot of a book or film, even by using very basic language. Cannot give a straightforward description of one of a variety of subjects within his/her field of interest. Does not keep going comprehensibly, and relies on pausing for grammatical and lexical planning most of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can connect only simple phrases in order to describe experiences and events, dreams, hopes and ambitions. Can briefly and at times incorrectly give reasons and explanations. Can narrate a story or relate the plot of a book or film, but only by using very basic language. Can give a straightforward description of one of a variety of subjects within his/her field of interest, presenting it as a linear sequence of points, even though not elegantly. Does not keep going comprehensibly, and relies on pausing for grammatical and lexical planning. Repair is very evident, both in short and longer stretches of free production.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can connect phrases in a simple way in order to describe experiences and events, my dreams, hopes and ambitions. Can briefly give reasons and explanations for opinions and plans. Can narrate a story or relate the plot of a book or film and describe my reactions. Can reasonably fluently sustain a straightforward description of one of a variety of subjects within his/her field of interest, presenting it as a linear sequence of points Can keep going comprehensibly, even though pausing for grammatical and lexical planning and repair is very evident, especially in longer stretches of free production. Can link a series of shorter, discrete simple elements into a connected, linear sequence of points.",
            ],
            [
                "Candidate performance below score 1.",
                "Does not have enough vocabulary to express him/ herself on topics such as family, hobbies and interests, work, travel, and current events. Shows almost no control of elementary vocabulary and makes major errors most of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has enough vocabulary to express him/ herself on topics such as family, hobbies and interests, work, travel, and current events but uses a lot of hesitation and circumlocutions. Does not have a sufficient range of language to describe unpredictable situations, explain the main points in an idea or problem with reasonable precision and express thoughts on abstract or cultural topics such as music and films. Shows some control of elementary vocabulary but major errors still occur on a regular basis.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has enough language to get by, with sufficient vocabulary to express him/ herself with some hesitation and circumlocutions on topics such as family, hobbies and interests, work, travel, and current events. Has a sufficient range of language to describe unpredictable situations, explain the main points in an idea or problem with reasonable precision and express thoughts on abstract or cultural topics such as music and films. Shows good control of elementary vocabulary but major errors still occur when expressing more complex thoughts or handling unfamiliar topics and situations",
            ],
            [
                "Candidate performance below score 1.",
                "Communicates with very little accuracy in familiar contexts. Has poor grammatical control most of the time. Errors occur often, and it is unclear what he/she is trying to express most of the time. Does not have a repertoire of frequently used “routines” and patterns associated with more predictable situations.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Communicates with some accuracy in familiar contexts; poor grammatical control though with noticeable mother tongue influence. Errors occur, and it is unclear at times what he/she is trying to express. Does not use a repertoire of frequently used “routines” and patterns associated with more predictable situations as accurately as expected at this level.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Communicates with reasonable accuracy in familiar contexts; generally good control though with noticeable mother tongue influence. Errors occur, but it is clear what he/she is trying to express. Uses reasonably accurately a repertoire of frequently used “routines” and patterns associated with more predictable situations.",
            ],
            [
                "Candidate performance below score 1.",
                "Pronunciation is not intelligible; a foreign accent is very evident and mispronunciations occur constantly.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Pronunciation is not intelligible some of the time, a foreign accent is sometimes evident and occasional mispronunciations occur",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Pronunciation is clearly intelligible even if a foreign accent is sometimes evident and occasional mispronunciations occur",
            ],
        ],
        [
            [
                "Candidate performance below score 1.",
                "Struggles to maintain a level of interaction with native speakers, and with mostly inaccurate use of simple language. Is not able to initiate, maintain and close simple face-to-face conversation, even on topics that are familiar or of personal interest. Does not repeat back part of what someone has said to confirm mutual understanding.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Maintains some level of interaction with native speakers on a variety of themes, with some (although not always) accurate use of simple language. Is able to initiate, maintain and close simple face-to-face conversation on topics that are familiar or of personal interest, although does not do this elegantly. Can repeat back at times part of what someone has said to confirm mutual understanding.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Maintain some level of interaction with native speakers on a variety of themes, mainly through accurate use of simple language. Can initiate, maintain and close simple face-to-face conversation on topics that are familiar or of personal interest. Can repeat back part of what someone has said to confirm mutual understanding.",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to use even simple language in order to describe experiences and events, dreams, hopes and ambitions. Is unable to narrate a story or relate the plot of a book or film, even by using very basic language. Cannot give a straightforward description of one of a variety of subjects within his/her field of interest. Does not keep going comprehensibly, and relies on pausing for grammatical and lexical planning most of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can connect only simple phrases in order to describe experiences and events, dreams, hopes and ambitions. Can briefly and at times incorrectly give reasons and explanations. Can narrate a story or relate the plot of a book or film, but only by using very basic language. Can give a straightforward description of one of a variety of subjects within his/her field of interest, presenting it as a linear sequence of points, even though not elegantly. Does not keep going comprehensibly, and relies on pausing for grammatical and lexical planning. Repair is very evident, both in short and longer stretches of free production.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can connect phrases in a simple way in order to describe experiences and events, my dreams, hopes and ambitions. Can briefly give reasons and explanations for opinions and plans. Can narrate a story or relate the plot of a book or film and describe my reactions. Can reasonably fluently sustain a straightforward description of one of a variety of subjects within his/her field of interest, presenting it as a linear sequence of points Can keep going comprehensibly, even though pausing for grammatical and lexical planning and repair is very evident, especially in longer stretches of free production. Can link a series of shorter, discrete simple elements into a connected, linear sequence of points.",
            ],
            [
                "Candidate performance below score 1.",
                "Does not have enough vocabulary to express him/ herself on topics such as family, hobbies and interests, work, travel, and current events. Shows almost no control of elementary vocabulary and makes major errors most of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has enough vocabulary to express him/ herself on topics such as family, hobbies and interests, work, travel, and current events but uses a lot of hesitation and circumlocutions. Does not have a sufficient range of language to describe unpredictable situations, explain the main points in an idea or problem with reasonable precision and express thoughts on abstract or cultural topics such as music and films. Shows some control of elementary vocabulary but major errors still occur on a regular basis.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has enough language to get by, with sufficient vocabulary to express him/ herself with some hesitation and circumlocutions on topics such as family, hobbies and interests, work, travel, and current events. Has a sufficient range of language to describe unpredictable situations, explain the main points in an idea or problem with reasonable precision and express thoughts on abstract or cultural topics such as music and films. Shows good control of elementary vocabulary but major errors still occur when expressing more complex thoughts or handling unfamiliar topics and situations",
            ],
            [
                "Candidate performance below score 1.",
                "Communicates with very little accuracy in familiar contexts. Has poor grammatical control most of the time. Errors occur often, and it is unclear what he/she is trying to express most of the time. Does not have a repertoire of frequently used “routines” and patterns associated with more predictable situations.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Communicates with some accuracy in familiar contexts; poor grammatical control though with noticeable mother tongue influence. Errors occur, and it is unclear at times what he/she is trying to express. Does not use a repertoire of frequently used “routines” and patterns associated with more predictable situations as accurately as expected at this level.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Communicates with reasonable accuracy in familiar contexts; generally good control though with noticeable mother tongue influence. Errors occur, but it is clear what he/she is trying to express. Uses reasonably accurately a repertoire of frequently used “routines” and patterns associated with more predictable situations.",
            ],
            [
                "Candidate performance below score 1.",
                "Pronunciation is not intelligible; a foreign accent is very evident and mispronunciations occur constantly.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Pronunciation is not intelligible some of the time, a foreign accent is sometimes evident and occasional mispronunciations occur",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Pronunciation is clearly intelligible even if a foreign accent is sometimes evident and occasional mispronunciations occur",
            ],
        ],
    ],
    "B2" => [
        [
            [
                "Candidate performance below score 1.",
                "Does not express him or herself confidently, clearly and politely, neither in a formal nor informal register. Can convey very low degrees of emotion and is not able to highlight the personal significance of events and experiences.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can express him or herself somewhat confidently, clearly and politely in a formal or informal register. Can convey some degrees of emotion and highlight some of the personal significance of events and experiences.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can express him or herself confidently, clearly and politely in a formal or informal register, appropriate to the situation and person(s) concerned. Can convey degrees of emotion and highlight the personal significance of events and experiences.",
            ],
            [
                "Candidate performance below score 1.",
                "Is unable to use even basic strategies to achieve comprehension. Does not understand a lot of the time what is said to him/her in the standard spoken language. Cannot sustain relationships with native speakers as will unintentionally amuse or irritate them, and require them to behave other than they would with a native speaker most of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can use only some basic strategies to achieve comprehension. Can understand, but not in detail, what is said to him/her in the standard spoken language. Can sustain relationships with native speakers  but will unintentionally amuse or irritate them at times and sometimes require them to behave other than they would with a native speaker.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can use a variety of strategies to achieve comprehension, including listening for main points; checking comprehension by using contextual clues. Can understand in detail what is said to him/her in the standard spoken language even in a noisy environment. Can sustain relationships with native speakers without unintentionally amusing or irritating them or requiring them to behave other than they would with a native speaker.",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to interact with native speakers, and the degree of fluency and spontaneity makes regular interaction with native speakers difficult at all times. Does not take an active part in discussion in familiar contexts. Does not initiate discourse, is unable to take her/his turn when appropriate and end conversation when he/she needs to. Does not help the discussion along. Communicates with a restricted degree of grammatical control.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can interact with native speakers, although the degree of fluency and spontaneity makes regular interaction with native speakers difficult at times. Can take an active part in discussion in familiar contexts, accounting for and sustaining their views, although struggles to do so elegantly. Does not always initiate discourse, take her/his turn when appropriate and end conversation when he/she needs to. Can help the discussion along at times. Can communicate with some degree of grammatical control although shows some signs of having to restrict what he /she wants to say.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can interact with a degree of fluency and spontaneity that makes regular interaction with native speakers possible. Can take an active part in discussion in familiar contexts, accounting for and sustaining their views. Can initiate discourse, take her/his turn when appropriate and end conversation when he/she needs to, though he/she may not always do so elegantly. Can help the discussion along on familiar ground confirming comprehension, inviting others in etc. Can communicate spontaneously with good grammatical control without much sign of having to restrict what he /she wants to say, adopting a level of formality appropriate to the circumstances.",
            ],
            [
                "Candidate performance below score 1.",
                "Is unable to present descriptions on subjects related to their field of interest, and when he/she tries, it is not clear or detailed. Cannot explain a viewpoint on a topical issue giving the advantages and disadvantages of various options. Does not exhibit a language level that will allow he/she to produce stretches of language. Speech is full of noticeable long pauses.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can present some descriptions on a range of subjects related to their field of interest, although this is not always clear or detailed. Can explain a viewpoint on a topical issue giving the advantages and disadvantages of various options, although struggles to do so. Produces stretches of language with an uneven tempo. There are quite a few noticeable long pauses.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can present clear, detailed descriptions on a wide range of subjects related to their field of interest. Can explain a viewpoint on a topical issue giving the advantages and disadvantages of various options. Can produce stretches of language with a fairly even tempo; although he/she can be hesitant as he/she searches for patterns and expressions. There are few noticeable long pauses. Can use a limited number of cohesive devices to link his/her utterances into clear, coherent discourse, though there may be some “jumpiness” in a long contribution",
            ],
            [
                "Candidate performance below score 1.",
                "Does not have a sufficient range of language to be able to give clear descriptions or express viewpoints on most general topics. Does not have a good range of vocabulary for matters connected to his / her field or more general topics. Displays lexical gaps that would indicate a lower level; confusion and incorrect word choice occur regularly which hinders communication.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a sufficient range of language to be able to give clear descriptions, express viewpoints on most general topics, although this comes with conspicuous searching for words. Has a good range of vocabulary for matters connected to his / her field, although struggles with general topics. Lexical gaps can cause hesitation and circumlocution. Lexical accuracy is not as high as expected. Confusion and incorrect word choice occur which hinders communication at times.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a sufficient range of language to be able to give clear descriptions, express viewpoints on most general topics, without much conspicuous searching for words, using some complex sentence forms to do so. Has a very good range of vocabulary for matters connected to his / her field and most general topics. Can vary formulation to avoid frequent repetition, but lexical gaps can still cause hesitation and circumlocution. Lexical accuracy is generally high, though some confusion and incorrect word choice does occur without hindering communication",
            ],
            [
                "Candidate performance below score 1.",
                "Shows a degree of grammatical control much lower than expected for an upper intermediate user. Makes constant errors which cause misunderstanding, and does not correct any of his/her mistakes.  Repeated systematic errors and flaws in sentence structure occur and are not corrected in retrospect.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Shows a degree of grammatical control lower than expected for an upper intermediate user. Makes errors which cause misunderstanding, and does not correct most of his/her mistakes.  Occasional systematic errors and minor flaws in sentence structure may still occur but they are corrected in retrospect most of the time.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Shows a relatively high degree of grammatical control. Does not make errors which cause misunderstanding, and can correct most of his/her mistakes. Good grammatical control; occasional “slips” or non-systematic errors and minor flaws in sentence structure may still occur, but they are rare and can often be corrected in retrospect",
            ],
        ],
        [
            [
                "Candidate performance below score 1.",
                "Does not use any strategies to achieve comprehension, and struggles to understand any of the instructions given.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Uses some strategies to achieve comprehension, but struggles to understand detailed instructions reliably.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can use a variety of strategies to achieve comprehension, including listening for main points; checking comprehension by using contextual clues. Can understand detailed instructions reliably",
            ],
            [
                "Candidate performance below score 1.",
                "Does not develop points of view and arguments with clarity, and struggles to contribute to a back and forth discussion/argument. Does not account for his/her opinions in discussion and does not provide any explanations, arguments and comments. Is unable to outline an issue or problem.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can develop points of view and arguments with some clarity, particularly in areas of interest, and sometimes contribute to a back and forth discussion/argument. Accounts for his/her opinions in discussion by providing some explanations, arguments and comments, although does not so elegantly or accurately. Can outline an issue or problem, even though not clearly.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can develop points of view and arguments with high clarity, particularly in areas of interest, and contribute to a back and forth discussion/argument. Can account for and sustain his/her opinions in discussion by providing relevant explanations, arguments and comments. Can express his/her ideas and opinions with precision, present and respond to complex lines of argument convincingly. Can outline an issue or problem clearly, speculating about causes or consequences, and weighing advantages and disadvantages of different approaches",
            ],
            [
                "Candidate performance below score 1.",
                "Is unable to present descriptions on subjects related to their field of interest, and when he/she tries, it is not clear or detailed. Cannot explain a viewpoint on a topical issue giving the advantages and disadvantages of various options. Does not exhibit a language level that will allow he/she to produce stretches of language. Speech is full of noticeable long pauses.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can present some descriptions on a range of subjects related to their field of interest, although this is not always clear or detailed. Can explain a viewpoint on a topical issue giving the advantages and disadvantages of various options, although struggles to do so. Produces stretches of language with an uneven tempo. There are quite a few noticeable long pauses.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can present clear, detailed descriptions on a wide range of subjects related to their field of interest. Can explain a viewpoint on a topical issue giving the advantages and disadvantages of various options. Can produce stretches of language with a fairly even tempo; although he/she can be hesitant as he/she searches for patterns and expressions. There are few noticeable long pauses. Can use a limited number of cohesive devices to link his/her utterances into clear, coherent discourse, though there may be some “jumpiness” in a long contribution",
            ],
            [
                "Candidate performance below score 1.",
                "Does not have a sufficient range of language to be able to give clear descriptions or express viewpoints on most general topics. Does not have a good range of vocabulary for matters connected to his / her field or more general topics. Displays lexical gaps that would indicate a lower level; confusion and incorrect word choice occur regularly which hinders communication.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a sufficient range of language to be able to give clear descriptions, express viewpoints on most general topics, although this comes with conspicuous searching for words. Has a good range of vocabulary for matters connected to his / her field, although struggles with general topics. Lexical gaps can cause hesitation and circumlocution. Lexical accuracy is not as high as expected. Confusion and incorrect word choice occur which hinders communication at times.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a sufficient range of language to be able to give clear descriptions, express viewpoints on most general topics, without much conspicuous searching for words, using some complex sentence forms to do so. Has a very good range of vocabulary for matters connected to his / her field and most general topics. Can vary formulation to avoid frequent repetition, but lexical gaps can still cause hesitation and circumlocution. Lexical accuracy is generally high, though some confusion and incorrect word choice does occur without hindering communication",
            ],
            [
                "Candidate performance below score 1.",
                "Shows a degree of grammatical control much lower than expected for an upper intermediate user. Makes constant errors which cause misunderstanding, and does not correct any of his/her mistakes.  Repeated systematic errors and flaws in sentence structure occur and are not corrected in retrospect.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Shows a degree of grammatical control lower than expected for an upper intermediate user. Makes errors which cause misunderstanding, and does not correct most of his/her mistakes.  Occasional systematic errors and minor flaws in sentence structure may still occur but they are corrected in retrospect most of the time.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Shows a relatively high degree of grammatical control. Does not make errors which cause misunderstanding, and can correct most of his/her mistakes. Good grammatical control; occasional “slips” or non-systematic errors and minor flaws in sentence structure may still occur, but they are rare and can often be corrected in retrospect",
            ],
            [
                "Candidate performance below score 1.",
                "Pronunciation unclear a lot of the time, and foreign accent often impedes communication.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Pronunciation clear some if not most of the time. Has a definite foreign accent, but it does not impede communication or intonation.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has acquired a clear, natural, pronunciation and intonation",
            ],
        ],
        [
            [
                "Candidate performance below score 1.",
                "Struggles to interact, and the degree of fluency and spontaneity makes regular interaction difficult at all times. Does not take an active part in discussion in familiar contexts. Does not initiate discourse, is unable to take her/his turn when appropriate and end conversation when he/she needs to. Does not help the discussion along. Communicates with a restricted degree of grammatical control.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can interact with others, although the degree of fluency and spontaneity makes regular interaction difficult at times. Can take an active part in discussion in familiar contexts, accounting for and sustaining their views, although struggles to do so elegantly. Does not always initiate discourse, take her/his turn when appropriate and end conversation when he/she needs to. Can help the discussion along at times. Can communicate with some degree of grammatical control although shows some signs of having to restrict what he /she wants to say.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can interact with a degree of fluency and spontaneity that makes regular interaction with native speakers possible. Can take an active part in discussion in familiar contexts, accounting for and sustaining their views. Can initiate discourse, take her/his turn when appropriate and end conversation when he/she needs to, though he/she may not always do so elegantly. Can help the discussion along on familiar ground confirming comprehension, inviting others in etc. Can communicate spontaneously with good grammatical control without much sign of having to restrict what he /she wants to say, adopting a level of formality appropriate to the circumstances.",
            ],
            [
                "Candidate performance below score 1.",
                "Does not develop points of view and arguments with clarity, and struggles to contribute to a back and forth discussion/argument. Does not account for his/her opinions in discussion and does not provide any explanations, arguments and comments. Is unable to outline an issue or problem.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can develop points of view and arguments with some clarity, particularly in areas of interest, and sometimes contribute to a back and forth discussion/argument. Accounts for his/her opinions in discussion by providing some explanations, arguments and comments, although does not so elegantly or accurately. Can outline an issue or problem, even though not clearly.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can develop points of view and arguments with high clarity, particularly in areas of interest, and contribute to a back and forth discussion/argument. Can account for and sustain his/her opinions in discussion by providing relevant explanations, arguments and comments. Can express his/her ideas and opinions with precision, present and respond to complex lines of argument convincingly. Can outline an issue or problem clearly, speculating about causes or consequences, and weighing advantages and disadvantages of different approaches",
            ],
            [
                "Candidate performance below score 1.",
                "Is unable to present descriptions on subjects related to their field of interest, and when he/she tries, it is not clear or detailed. Cannot explain a viewpoint on a topical issue giving the advantages and disadvantages of various options. Does not exhibit a language level that will allow he/she to produce stretches of language. Speech is full of noticeable long pauses.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can present some descriptions on a range of subjects related to their field of interest, although this is not always clear or detailed. Can explain a viewpoint on a topical issue giving the advantages and disadvantages of various options, although struggles to do so. Produces stretches of language with an uneven tempo. There are quite a few noticeable long pauses.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can present clear, detailed descriptions on a wide range of subjects related to their field of interest. Can explain a viewpoint on a topical issue giving the advantages and disadvantages of various options. Can produce stretches of language with a fairly even tempo; although he/she can be hesitant as he/she searches for patterns and expressions. There are few noticeable long pauses. Can use a limited number of cohesive devices to link his/her utterances into clear, coherent discourse, though there may be some “jumpiness” in a long contribution",
            ],
            [
                "Candidate performance below score 1.",
                "Does not have a sufficient range of language to be able to give clear descriptions or express viewpoints on most general topics. Does not have a good range of vocabulary for matters connected to his / her field or more general topics. Displays lexical gaps that would indicate a lower level; confusion and incorrect word choice occur regularly which hinders communication.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a sufficient range of language to be able to give clear descriptions, express viewpoints on most general topics, although this comes with conspicuous searching for words. Has a good range of vocabulary for matters connected to his / her field, although struggles with general topics. Lexical gaps can cause hesitation and circumlocution. Lexical accuracy is not as high as expected. Confusion and incorrect word choice occur which hinders communication at times.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a sufficient range of language to be able to give clear descriptions, express viewpoints on most general topics, without much conspicuous searching for words, using some complex sentence forms to do so. Has a very good range of vocabulary for matters connected to his / her field and most general topics. Can vary formulation to avoid frequent repetition, but lexical gaps can still cause hesitation and circumlocution. Lexical accuracy is generally high, though some confusion and incorrect word choice does occur without hindering communication",
            ],
            [
                "Candidate performance below score 1.",
                "Shows a degree of grammatical control much lower than expected for an upper intermediate user. Makes constant errors which cause misunderstanding, and does not correct any of his/her mistakes.  Repeated systematic errors and flaws in sentence structure occur and are not corrected in retrospect.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Shows a degree of grammatical control lower than expected for an upper intermediate user. Makes errors which cause misunderstanding, and does not correct most of his/her mistakes.  Occasional systematic errors and minor flaws in sentence structure may still occur but they are corrected in retrospect most of the time.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Shows a relatively high degree of grammatical control. Does not make errors which cause misunderstanding, and can correct most of his/her mistakes. Good grammatical control; occasional “slips” or non-systematic errors and minor flaws in sentence structure may still occur, but they are rare and can often be corrected in retrospect",
            ],
            [
                "Candidate performance below score 1.",
                "Pronunciation unclear a lot of the time, and foreign accent often impedes communication.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Pronunciation clear some if not most of the time. Has a definite foreign accent, but it does not impede communication or intonation.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has acquired a clear, natural, pronunciation and intonation. Sentence and word stress is generally accurately placed, and individual sounds are generally articulated clearly.",
            ],
        ],
    ],
    "C1" => [
        [
            [
                "Candidate performance below score 1.",
                "Struggles to use language for social purposes. Does not recognise most idiomatic expressions and colloquialisms, and constantly needs to confirm details. Is not skilled at using contextual, grammatical and lexical cues to infer attitude, mood and intentions and anticipate what will come next. Finds it difficult to backtrack when he/she encounters a difficulty and struggles to reformulate what he/she wants to say.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can use language for social purposes, including emotional, allusive and joking usage. Can recognise some idiomatic expressions and colloquialisms; needs to confirm details, especially if the accent is unfamiliar. Is somewhat skilled at using contextual, grammatical and lexical cues to infer attitude, mood and intentions and anticipate what will come next. Finds it difficult to, but can backtrack when he/she encounters a difficulty and can reformulate (inelegantly) what he/she wants to say.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can use language flexibly and effectively for social purposes, including emotional, allusive and joking usage. Can recognise a wide range of idiomatic expressions and colloquialisms, appreciating register shifts; may, however, need to confirm occasional details, especially if the accent is unfamiliar. Is skilled at using contextual, grammatical and lexical cues to infer attitude, mood and intentions and anticipate what will come next. Can backtrack when he/she encounters a difficulty and reformulate what he/she wants to say without fully interrupting the flow of speech",
            ],
            [
                "Candidate performance below score 1.",
                "Does not express him or herself fluently and there is an obvious search for expressions. Can formulate ideas and opinions with a lack of precision and struggles to relate their contribution to those of other speakers. Is not skilled in getting or keeping the floor or relating his/her own contribution to those of other speakers.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can express him or herself fluently with some obvious search for expressions. Can formulate ideas and opinions with a certain degree of precision and relate their contribution to those of other speakers, even if not skilfully. Uses some discourse functions in order to get or to keep the floor and to relate his/her own contribution to those of other speakers, but does not do so elegantly. There is a fairly obvious searching for expressions or avoidance strategies.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can express him or herself fluently and spontaneously without much obvious search for expressions. Can formulate ideas and opinions with precision and relate their contribution skilfully to those of other speakers. Can select a suitable phrase from a readily available range of discourse functions to preface their remarks in order to get or to keep the floor and to relate his/her own contribution skilfully to those of other speakers. There is little obvious searching for expressions or avoidance strategies; only a conceptually difficult subject can hinder a natural, smooth flow of language.",
            ],
            [
                "Candidate performance below score 1.",
                "Is unable to understand speech on abstract and complex topics of a specialist nature beyond his/ her own field.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can understand some speech on abstract and complex topics of a specialist nature beyond his/ her own field, though he/she needs to confirm details constantly.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can understand in detail speech on abstract and complex topics of a specialist nature beyond his/ her own field, though he/she may need to confirm occasional details, especially if the accent is unfamiliar.",
            ],
            [
                "Candidate performance below score 1.",
                "Does not express him/herself fluently, and it is apparent that a lot of effort is involved. Does not exhibit a natural, smooth flow of language. Is unable to present detailed descriptions of complex subjects and struggles to organise speech. Does not produce clear, well-structured speech, and is unable to use organisational patterns, connectors and cohesive devices.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can express him/herself fluently some of the time, and it is apparent that some effort is involved. Displays a natural, smooth flow of language at times, but struggles at other times. Can present some detailed descriptions of complex subjects but struggles to organise speech at an advanced level. Can produce some clear, well-structured speech, showing use of some organisational patterns, connectors and cohesive devices.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can express him/herself fluently and spontaneously, almost effortlessly. Only a conceptually difficult subject can hinder a natural, smooth flow of language. Can present clear, detailed descriptions of complex subjects integrating sub-themes, developing particular points and rounding off with an appropriate conclusion. Can produce clear, smoothly flowing, well-structured speech, showing controlled use of organisational patterns, connectors and cohesive devices.",
            ],
            [
                "Candidate performance below score 1.",
                "Displays a relatively low degree of grammatical accuracy; errors do occur regularly and are not corrected most / a lot of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Maintains a relatively high degree of grammatical accuracy; errors do occur and are only sometimes corrected.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Consistently maintains a high degree of grammatical accuracy; errors are rare, difficult to spot and generally corrected when they occur.",
            ],
            [
                "Candidate performance below score 1.",
                "Has a relatively low command of a range of language that prevents him/ her from selecting a formulation to express him/herself. Can select an appropriate formulation from a relatively broad range of language to express him/herself somewhat clearly. Several significant vocabulary errors, with most of them not corrected.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a relatively good command of a range of language that allows him / her to select a formulation to express him/herself in a relatively appropriate style on a range of general, academic, professional or leisure topics, although he/she has to restrict what he/she wants to say. Can select an appropriate formulation from a relatively broad range of language to express him/herself somewhat clearly. Occasional significant vocabulary errors, out of which only some are corrected.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a good command of a broad range of language allowing him / her to select a formulation to express him/herself clearly in an appropriate style on a wide range of general, academic, professional or leisure topics without having to restrict what he/she wants to say. Can select an appropriate formulation from a broad range of language to express him/herself clearly, without having to restrict what he/she wants to say. Occasional minor slips, but no significant vocabulary errors",
            ],
        ],
        [
            [
                "Candidate performance below score 1.",
                "Formulates ideas and opinions with a low degree of precision and struggles to relate their contribution to those of other speakers. Is unable to select a suitable phrase from a range of discourse functions to preface his/her remarks in order to get or to keep the floor and to relate his/her own contribution to those of other speakers. Struggles to follow and contribute to complex interactions between third parties in group discussion and is unable to deal with with abstract, complex unfamiliar topics. Is unable to keep up with the debate.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can formulate ideas and opinions with some precision and relate their contribution to those of other speakers. Can select a suitable phrase from a range of discourse functions to preface his/her remarks in order to get or to keep the floor and to relate his/her own contribution to those of other speakers, although does not do so elegantly. Can follow and contribute to complex interactions between third parties in group discussion but struggles with abstract, complex unfamiliar topics. Can keep up with the debate with a certain degree of effort. Can argue a formal position, respond to questions and comments and answer certain complex lines of counter argument although lacks fluency and spontaneity.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can formulate ideas and opinions with precision and relate their contribution skilfully to those of other speakers. Can select a suitable phrase from a readily available range of discourse functions to preface his/her remarks in order to get or to keep the floor and to relate his/her own contribution skilfully to those of other speakers. Can easily follow and contribute to complex interactions between third parties in group discussion even on abstract, complex unfamiliar topics. Can easily keep up with the debate, even on abstract, complex unfamiliar topics. Can argue a formal position convincingly, responding to questions and comments and answering complex lines of counter argument fluently, spontaneously and appropriately.",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to present descriptions of complex subjects and  does not organise the speech structure by integrating sub-themes, developing particular points and rounding off with an appropriate conclusion. Is unable to produce a speech that shows controlled use of organisational patterns, connectors and cohesive devices.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can present some descriptions of complex subjects although does not always organise the speech structure by integrating sub-themes, developing particular points and rounding off with an appropriate conclusion. Can produce a speech that shows controlled use of organisational patterns, connectors and cohesive devices, albeit not at all times.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can present clear, detailed descriptions of complex subjects integrating sub-themes, developing particular points and rounding off with an appropriate conclusion. Can produce clear, smoothly flowing, well-structured speech, showing controlled use of organisational patterns, connectors and cohesive devices.",
            ],
            [
                "Candidate performance below score 1.",
                "Does not express him/herself fluently, and it is apparent that a lot of effort is involved. Does not exhibit a natural, smooth flow of language. Is unable to present detailed descriptions of complex subjects and struggles to organise speech. Does not produce clear, well-structured speech, and is unable to use organisational patterns, connectors and cohesive devices.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can express him/herself fluently some of the time, and it is apparent that some effort is involved. Displays a natural, smooth flow of language at times, but struggles at other times. Can present some detailed descriptions of complex subjects but struggles to organise speech at an advanced level. Can produce some clear, well-structured speech, showing use of some organisational patterns, connectors and cohesive devices.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can express him/herself fluently and spontaneously, almost effortlessly. Only a conceptually difficult subject can hinder a natural, smooth flow of language. Can present clear, detailed descriptions of complex subjects integrating sub-themes, developing particular points and rounding off with an appropriate conclusion. Can produce clear, smoothly flowing, well structured speech, showing controlled use of organisational patterns, connectors and cohesive devices.",
            ],
            [
                "Candidate performance below score 1.",
                "Displays a relatively low degree of grammatical accuracy; errors do occur regularly and are not corrected most / a lot of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Maintains a relatively high degree of grammatical accuracy; errors do occur and are only sometimes corrected.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Consistently maintains a high degree of grammatical accuracy; errors are rare, difficult to spot and generally corrected when they occur.",
            ],
            [
                "Candidate performance below score 1.",
                "Has a relatively low command of a range of language that prevents him/ her from selecting a formulation to express him/herself. Can select an appropriate formulation from a relatively broad range of language to express him/herself somewhat clearly. Several significant vocabulary errors, with most of them not corrected.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a relatively good command of a range of language that allows him / her to select a formulation to express him/herself in a relatively appropriate style on a range of general, academic, professional or leisure topics, although he/she has to restrict what he/she wants to say. Can select an appropriate formulation from a relatively broad range of language to express him/herself somewhat clearly. Occasional significant vocabulary errors, out of which only some are corrected.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a good command of a broad range of language allowing him / her to select a formulation to express him/herself clearly in an appropriate style on a wide range of general, academic, professional or leisure topics without having to restrict what he/she wants to say. Can select an appropriate formulation from a broad range of language to express him/herself clearly, without having to restrict what he/she wants to say. Occasional minor slips, but no significant vocabulary errors",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to place sentence stress correctly. Is not always intelligible. Intonation is only at times appropriate and sentence and word stress are mostly not accurately placed.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Is able to place sentence stress correctly at times. Is somewhat intelligible. Intonation is sometimes appropriate. Sentence and word stress are accurately placed some of the time.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can vary intonation and place sentence stress correctly in order to express finer shades of meaning. Is intelligible and intonation is appropriate and sentence and word stress is accurately placed.",
            ],
        ],
        [
            [
                "Candidate performance below score 1.",
                "Struggles to present descriptions of complex subjects and does not organise the speech structure by integrating sub-themes, developing particular points and rounding off with an appropriate conclusion. Is unable to produce a speech that shows controlled use of organisational patterns, connectors and cohesive devices.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can present some descriptions of complex subjects although does not always organise the speech structure by integrating sub-themes, developing particular points and rounding off with an appropriate conclusion. Can produce a speech that shows controlled use of organisational patterns, connectors and cohesive devices, albeit not at all times.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can present clear, detailed descriptions of complex subjects integrating sub-themes, developing particular points and rounding off with an appropriate conclusion. Can give clear, detailed descriptions and presentations on complex subjects, integrating sub-themes, developing particular points and rounding off with an appropriate conclusion. Can produce clear, smoothly flowing, well-structured speech, showing controlled use of organisational patterns, connectors and cohesive devices.",
            ],
            [
                "Candidate performance below score 1.",
                "Does not express him/herself fluently, and it is apparent that a lot of effort is involved. Does not exhibit a natural, smooth flow of language. Is unable to present detailed descriptions of complex subjects and struggles to organise speech. Does not produce clear, well-structured speech, and is unable to use organisational patterns, connectors and cohesive devices.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can express him/herself fluently some of the time, and it is apparent that some effort is involved. Displays a natural, smooth flow of language at times, but struggles at other times. Can present some detailed descriptions of complex subjects but struggles to organise speech at an advanced level. Can produce some clear, well-structured speech, showing use of some organisational patterns, connectors and cohesive devices.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can express him/herself fluently and spontaneously, almost effortlessly. Only a conceptually difficult subject can hinder a natural, smooth flow of language. Can present clear, detailed descriptions of complex subjects integrating sub-themes, developing particular points and rounding off with an appropriate conclusion. Can produce clear, smoothly flowing, well structured speech, showing controlled use of organisational patterns, connectors and cohesive devices.",
            ],
            [
                "Candidate performance below score 1.",
                "Displays a relatively low degree of grammatical accuracy; errors do occur regularly and are not corrected most / a lot of the time.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Maintains a relatively high degree of grammatical accuracy; errors do occur and are only sometimes corrected.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Consistently maintains a high degree of grammatical accuracy; errors are rare, difficult to spot and generally corrected when they occur.",
            ],
            [
                "Candidate performance below score 1.",
                "Has a relatively low command of a range of language that prevents him/ her from selecting a formulation to express him/herself. Can select an appropriate formulation from a relatively broad range of language to express him/herself somewhat clearly. Several significant vocabulary errors, with most of them not corrected.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Has a relatively good command of a range of language that allows him / her to select a formulation to express him/herself in a relatively appropriate style on a range of general, academic, professional or leisure topics, although he/she has to restrict what he/she wants to say. Can select an appropriate formulation from a relatively broad range of language to express him/herself somewhat clearly. Occasional significant vocabulary errors, out of which only some are corrected.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Has a good command of a broad range of language allowing him / her to select a formulation to express him/herself clearly in an appropriate style on a wide range of general, academic, professional or leisure topics without having to restrict what he/she wants to say. Can select an appropriate formulation from a broad range of language to express him/herself clearly, without having to restrict what he/she wants to say. Occasional minor slips, but no significant vocabulary errors",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to place sentence stress correctly. Is not always intelligible. Intonation is only at times appropriate and sentence and word stress are mostly not accurately placed.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Is able to place sentence stress correctly at times. Is somewhat intelligible. Intonation is sometimes appropriate. Sentence and word stress are accurately placed some of the time.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can vary intonation and place sentence stress correctly in order to express finer shades of meaning. Is intelligible and intonation is appropriate and sentence and word stress is accurately placed.",
            ],
        ],
    ],
    "C2" => [
        [
            [
                "Candidate performance below score 1.",
                "Does not appreciate the sociolinguistic and sociocultural implications of language used by native speakers and therefore does not react accordingly. Is unable to mediate between speakers of the target language and that of his/her community of origin. Is not aware of idiomatic expressions and colloquialisms and has a low awareness of connotative levels of meaning.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Appreciates the sociolinguistic and sociocultural implications of language used by native speakers and can react accordingly, even though not always. Can mediate to an extent between speakers of the target language and that of his/her community of origin taking account of some sociocultural and sociolinguistic differences. Is aware of some idiomatic expressions and colloquialisms but has a low awareness of connotative levels of meaning.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Appreciates fully the sociolinguistic and sociocultural implications of language used by native speakers and can react accordingly. Can mediate effectively between speakers of the target language and that of his/her community of origin taking account of sociocultural and sociolinguistic differences. Has a good command of idiomatic expressions and colloquialisms with awareness of connotative levels of meaning.",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to interact with ease, and finds it difficult to pick up and use non-verbal and intonational cues. Is unable to use turn taking, referencing, allusion making etc well. Is not aware of the meaning of idiomatic expressions and colloquialisms. Cannot express themselves fluently and is unable to convey finer shades of meaning. When he/she encounters a difficulty, will try to backtrack and restructure around it but will struggle to do so.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can interact with relative ease, but makes an effort to pick up and use non-verbal and intonational cues. Uses turn taking, referencing, allusion making etc. but not elegantly. Can take part in a conversation or discussion but will struggle with idiomatic expressions and colloquialisms. Can express themselves fluently at time but struggles to convey finer shades of meaning precisely. When he/she encounters a difficulty, will try to backtrack and restructure around it but the interlocutor will be made aware of it.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can interact with ease, picking up and using non-verbal and intonational cues apparently effortlessly. Can deliver with fully natural turntaking, referencing, allusion making etc. Can take part effortlessly in any conversation or discussion and have a good familiarity with idiomatic expressions and colloquialisms. Can express themselves fluently and convey finer shades of meaning precisely. Can backtrack and restructure around a difficulty so smoothly the interlocutor is hardly aware of it.",
            ],
            [
                "Candidate performance below score 1.",
                "Will present a description or argument in a style somewhat appropriate to the context but with a logical structure which lead to the recipient being distracted and unable to notice and remember the main points. Will try to present a complex topic to an audience unfamiliar with it, but the structure and language used to do so will make it difficult to follow.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can present a clear description or argument in a style relatively appropriate to the context and with a somewhat logical structure which helps the recipient to notice and remember the main points. Can present a complex topic to an audience unfamiliar with it, but will make some mistakes under the pressure. Struggles to handle difficult and even hostile questioning.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can present a clear, smoothly flowing description or argument in a style appropriate to the context and with an effective logical structure which helps the recipient to notice and remember significant points. Can present a complex topic confidently and articulately to an audience unfamiliar with it, structuring and adapting the talk flexibly to meet the audience’s needs. Can handle difficult and even hostile questioning.",
            ],
            [
                "Candidate performance below score 1.",
                "Can express him/herself at length but will struggle and stumble at times, making the interlocutor acutely aware of the difficulties encountered.  Can deliver a discourse making use of organisational patterns and a relatively basic range of connectors and other cohesive devices.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can express him/herself at length with an even flow, although will sometimes encounter difficulties which he /she will avoid but only after making the interlocutor aware of it. Can create coherent discourse making somewhat appropriate use of organisational patterns and a range of connectors and other cohesive devices.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can express him/herself spontaneously at length with a natural colloquial flow, avoiding or backtracking around any difficulty so smoothly that the interlocutor is hardly aware of it. Can create coherent and cohesive discourse making full and appropriate use of a variety of organisational patterns and a wide range of connectors and other cohesive devices.",
            ],
            [
                "Candidate performance below score 1.",
                "Maintains relatively good grammatical control although does not use complex language and makes mistakes without even realising.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Maintains good grammatical control, even though avoids complex structures and sticks to more basic devices.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Maintains consistent grammatical control of complex language, even while attention is otherwise engaged (e.g. in forward planning, in monitoring others’ reactions).",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to reformulate ideas in differing linguistic forms. Does not understand all  idiomatic expressions and colloquialisms presented to him and uses some incorrectly. Does not understand the finer shades of meaning conveyed by a range of qualifying devices (e.g. adverbs expressing degree, clauses expressing limitations).",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Shows some flexibility reformulating ideas in differing linguistic forms, but does not do so at the level of a native English speaker. Understands some idiomatic expressions and colloquialisms. Can understand the finer shades of meaning conveyed by a range of qualifying devices (e.g. adverbs expressing degree, clauses expressing limitations) but is unable to use them all correctly.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Shows great flexibility reformulating ideas in differing linguistic forms to convey finer shades of meaning precisely, to give emphasis, to differentiate and to eliminate ambiguity. Also has a good command of idiomatic expressions and colloquialisms. Can understand the finer shades of meaning conveyed by a wide range of qualifying devices (e.g. adverbs expressing degree, clauses expressing limitations).",
            ],
            [
                "Candidate performance below score 1.",
                "Can understand a native speaker interlocutor some/most of the time. Will struggle to deal with abstract and complex topics of a specialist nature beyond his / her own field. Will ask for repetition often.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can understand a native speaker interlocutor, even though finds it more difficult to deal with abstract and complex topics of a specialist nature beyond his / her own field, and will ask for repetition in order to adjust to a non-standard accent or dialect",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can understand any native speaker interlocutor, even on abstract and complex topics of a specialist nature beyond his / her own field, given an opportunity to adjust to a non-standard accent or dialect",
            ],
        ],
        [
            [
                "Candidate performance below score 1.",
                "Does not appreciate the sociolinguistic and sociocultural implications of language used by native speakers and therefore does not react accordingly. Is unable to mediate between speakers of the target language and that of his/her community of origin. Is not aware of idiomatic expressions and colloquialisms and has a low awareness of connotative levels of meaning.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Appreciates the sociolinguistic and sociocultural implications of language used by native speakers and can react accordingly, even though not always. Can mediate to an extent between speakers of the target language and that of his/her community of origin taking account of some sociocultural and sociolinguistic differences. Is aware of some idiomatic expressions and colloquialisms but has a low awareness of connotative levels of meaning.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Appreciates fully the sociolinguistic and sociocultural implications of language used by native speakers and can react accordingly. Can mediate effectively between speakers of the target language and that of his/her community of origin taking account of sociocultural and sociolinguistic differences. Has a good command of idiomatic expressions and colloquialisms with awareness of connotative levels of meaning.",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to interact with ease, and finds it difficult to pick up and use non-verbal and intonational cues. Is unable to use turn taking, referencing, allusion making etc well. Is not aware of the meaning of idiomatic expressions and colloquialisms. Cannot express themselves fluently and is unable to convey finer shades of meaning. When he/she encounters a difficulty, will try to backtrack and restructure around it but will struggle to do so.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can interact with relative ease, but makes an effort to pick up and use non-verbal and intonational cues. Uses turn taking, referencing, allusion making etc. but not elegantly. Can take part in a conversation or discussion but will struggle with idiomatic expressions and colloquialisms. Can express themselves fluently at time but struggles to convey finer shades of meaning precisely. When he/she encounters a difficulty, will try to backtrack and restructure around it but the interlocutor will be made aware of it.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can interact with ease, picking up and using non-verbal and intonational cues apparently effortlessly. Can deliver with fully natural turntaking, referencing, allusion making etc. Can take part effortlessly in any conversation or discussion and have a good familiarity with idiomatic expressions and colloquialisms. Can express themselves fluently and convey finer shades of meaning precisely. Can backtrack and restructure around a difficulty so smoothly the interlocutor is hardly aware of it.",
            ],
            [
                "Candidate performance below score 1.",
                "Will present a description or argument in a style somewhat appropriate to the context but with a logical structure which lead to the recipient being distracted and unable to notice and remember the main points. Will try to present a complex topic to an audience unfamiliar with it, but the structure and language used to do so will make it difficult to follow.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can present a clear description or argument in a style relatively appropriate to the context and with a somewhat logical structure which helps the recipient to notice and remember the main points. Can present a complex topic to an audience unfamiliar with it, but will make some mistakes under the pressure. Struggles to handle difficult and even hostile questioning.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can present a clear, smoothly flowing description or argument in a style appropriate to the context and with an effective logical structure which helps the recipient to notice and remember significant points. Can present a complex topic confidently and articulately to an audience unfamiliar with it, structuring and adapting the talk flexibly to meet the audience’s needs. Can handle difficult and even hostile questioning.",
            ],
            [
                "Candidate performance below score 1.",
                "Can express him/herself at length but will struggle and stumble at times, making the interlocutor acutely aware of the difficulties encountered.  Can deliver a discourse making use of organisational patterns and a relatively basic range of connectors and other cohesive devices.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can express him/herself at length with an even flow, although will sometimes encounter difficulties which he /she will avoid but only after making the interlocutor aware of it. Can create coherent discourse making somewhat appropriate use of organisational patterns and a range of connectors and other cohesive devices.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can express him/herself spontaneously at length with a natural colloquial flow, avoiding or backtracking around any difficulty so smoothly that the interlocutor is hardly aware of it. Can create coherent and cohesive discourse making full and appropriate use of a variety of organisational patterns and a wide range of connectors and other cohesive devices.",
            ],
            [
                "Candidate performance below score 1.",
                "Maintains relatively good grammatical control although does not use complex language and makes mistakes without even realising.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Maintains good grammatical control, even though avoids complex structures and sticks to more basic devices.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Maintains consistent grammatical control of complex language, even while attention is otherwise engaged (e.g. in forward planning, in monitoring others’ reactions).",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to reformulate ideas in differing linguistic forms. Does not understand all  idiomatic expressions and colloquialisms presented to him and uses some incorrectly. Does not understand the finer shades of meaning conveyed by a range of qualifying devices (e.g. adverbs expressing degree, clauses expressing limitations).",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Shows some flexibility reformulating ideas in differing linguistic forms, but does not do so at the level of a native English speaker. Understands some idiomatic expressions and colloquialisms. Can understand the finer shades of meaning conveyed by a range of qualifying devices (e.g. adverbs expressing degree, clauses expressing limitations) but is unable to use them all correctly.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Shows great flexibility reformulating ideas in differing linguistic forms to convey finer shades of meaning precisely, to give emphasis, to differentiate and to eliminate ambiguity. Also has a good command of idiomatic expressions and colloquialisms. Can understand the finer shades of meaning conveyed by a wide range of qualifying devices (e.g. adverbs expressing degree, clauses expressing limitations).",
            ],
        ],
        [
            [
                "Candidate performance below score 1.",
                "Does not appreciate the sociolinguistic and sociocultural implications of language used by native speakers and therefore does not react accordingly. Is unable to mediate between speakers of the target language and that of his/her community of origin. Is not aware of idiomatic expressions and colloquialisms and has a low awareness of connotative levels of meaning.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Appreciates the sociolinguistic and sociocultural implications of language used by native speakers and can react accordingly, even though not always. Can mediate to an extent between speakers of the target language and that of his/her community of origin taking account of some sociocultural and sociolinguistic differences. Is aware of some idiomatic expressions and colloquialisms but has a low awareness of connotative levels of meaning.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Appreciates fully the sociolinguistic and sociocultural implications of language used by native speakers and can react accordingly. Can mediate effectively between speakers of the target language and that of his/her community of origin taking account of sociocultural and sociolinguistic differences. Has a good command of idiomatic expressions and colloquialisms with awareness of connotative levels of meaning.",
            ],
            [
                "Candidate performance below score 1.",
                "Will present a description or argument in a style somewhat appropriate to the context but with a logical structure which lead to the recipient being distracted and unable to notice and remember the main points. Will try to present a complex topic to an audience unfamiliar with it, but the structure and language used to do so will make it difficult to follow.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can present a clear description or argument in a style relatively appropriate to the context and with a somewhat logical structure which helps the recipient to notice and remember the main points. Can present a complex topic to an audience unfamiliar with it, but will make some mistakes under the pressure. Struggles to handle difficult and even hostile questioning.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can present a clear, smoothly flowing description or argument in a style appropriate to the context and with an effective logical structure which helps the recipient to notice and remember significant points. Can present a complex topic confidently and articulately to an audience unfamiliar with it, structuring and adapting the talk flexibly to meet the audience’s needs. Can handle difficult and even hostile questioning.",
            ],
            [
                "Candidate performance below score 1.",
                "Can express him/herself at length but will struggle and stumble at times, making the interlocutor acutely aware of the difficulties encountered.  Can deliver a discourse making use of organisational patterns and a relatively basic range of connectors and other cohesive devices.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can express him/herself at length with an even flow, although will sometimes encounter difficulties which he /she will avoid but only after making the interlocutor aware of it. Can create coherent discourse making somewhat appropriate use of organisational patterns and a range of connectors and other cohesive devices.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can express him/herself spontaneously at length with a natural colloquial flow, avoiding or backtracking around any difficulty so smoothly that the interlocutor is hardly aware of it. Can create coherent and cohesive discourse making full and appropriate use of a variety of organisational patterns and a wide range of connectors and other cohesive devices.",
            ],
            [
                "Candidate performance below score 1.",
                "Maintains relatively good grammatical control although does not use complex language and makes mistakes without even realising.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Maintains good grammatical control, even though avoids complex structures and sticks to more basic devices.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Maintains consistent grammatical control of complex language, even while attention is otherwise engaged (e.g. in forward planning, in monitoring others’ reactions).",
            ],
            [
                "Candidate performance below score 1.",
                "Struggles to reformulate ideas in differing linguistic forms. Does not understand all  idiomatic expressions and colloquialisms presented to him and uses some incorrectly. Does not understand the finer shades of meaning conveyed by a range of qualifying devices (e.g. adverbs expressing degree, clauses expressing limitations).",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Shows some flexibility reformulating ideas in differing linguistic forms, but does not do so at the level of a native English speaker. Understands some idiomatic expressions and colloquialisms. Can understand the finer shades of meaning conveyed by a range of qualifying devices (e.g. adverbs expressing degree, clauses expressing limitations) but is unable to use them all correctly.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Shows great flexibility reformulating ideas in differing linguistic forms to convey finer shades of meaning precisely, to give emphasis, to differentiate and to eliminate ambiguity. Also has a good command of idiomatic expressions and colloquialisms. Can understand the finer shades of meaning conveyed by a wide range of qualifying devices (e.g. adverbs expressing degree, clauses expressing limitations).",
            ],
            [
                "Candidate performance below score 1.",
                "Can vary intonation and place sentence stress to a relatively low extent in order to express finer shades of meaning. Sentence and word stress are accurately placed only at times. Some individual sounds are articulated clearly.",
                "Candidate performance shares characteristics of scores 1 and 3.",
                "Can vary intonation and place sentence stress to a certain extent in order to express finer shades of meaning. Sentence and word stress are mostly accurately placed. Most individual sounds are articulated clearly.",
                "Candidate performance shares characteristics of scores 3 and 5.",
                "Can vary intonation and place sentence stress correctly in order to express finer shades of meaning. Sentence and word stress are accurately placed. Individual sounds are articulated clearly.",
            ],
        ],
    ],
];
$modelNumber = $studentNumber - 1;
?>

<div class="row">
    <div class="col-xs-12">
        <button type="button" class="change-student btn-oxford boton-peque btn-dis" disabled>Change Student</button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <?= Select2::widget([
                'name' => 'institute',
                'data' => $instituteStudents,
                'options' => [
                    'placeholder' => 'Select',
                    'id' => "select-student-speaking-{$studentNumber}",
                    'data-container' => "student-{$studentNumber}",
                    'data-student' => $instituteStudents->id
                ],
            ]) ?>
        </div>
    </div>
</div>
<div class="row">
    <?php foreach ($levels as $level): ?>
    <div class="col-md-12 table <?= $level ?> hidden 0 ">
        <?php $form = ActiveForm::begin() ?>
            <?= $form->field($speakingModel, "[{$modelNumber}]student_id")->hiddenInput(['class' => 'input-student-id'])->label(false) ?>
            <?php $questionCounter = 0;
            foreach ($sectionTitlesByLevel[$level] as $sectionIndex => $section): ?>
            <div class="row">
                <div class="col-md-6">
                    <p class="speaking-section-title" id="speak-section-<?=$sectionIndex + 1?>" data-count="<?=count($fieldsToScoreByLevel[$level][$sectionIndex])?>" >SECTION <?= $sectionIndex + 1 ?> - <?= $section ?></p>
                </div>
                <div class="col-md-6">
                    <div class="number-wrapper">
                        <span class="number-columns">0</span>
                        <span class="number-columns">1</span>
                        <span class="number-columns">2</span>
                        <span class="number-columns">3</span>
                        <span class="number-columns">4</span>
                        <span class="number-columns">5</span>
                    </div>
                </div>
            </div>
                <?php foreach ($fieldsToScoreByLevel[$level][$sectionIndex] as $questionIndex => $question): ?>
                <div class="row">
                    <div class="col-md-6"><p><?= $question ?></p></div>
                    <div class="col-md-6">
                        <?= $form->field($speakingModel, "[{$modelNumber}]scores[{$questionCounter}]")
                            ->radioList($numberArray, [
                                'item' => function ($index, $label, $name, $checked, $value) use ($level, $sectionIndex, $questionIndex, $tooltipMessageByScore) {
                                    return '<label>'.
                                        Html::radio($name, $checked, [
                                            'value' => $value,
                                            'data-tooltip' => 'true',
                                            'title' => isset($tooltipMessageByScore[$level][$sectionIndex][$questionIndex][$index]) ? $tooltipMessageByScore[$level][$sectionIndex][$questionIndex][$index] : null
                                        ]).
                                        '</label>';
                                }
                            ])
                            ->label(false)
                        ?>
                    </div>
                </div>
                <?php $questionCounter++;
                endforeach; ?>
            <?php endforeach; ?>
            <div class="row observations-block">
                <div class="col-md-12">
                    <p class="toggle-observations">Observations</p>
                </div>
                <div class="col-md-12">
                    <?= $form->field($speakingModel, "[{$modelNumber}]observations")->textarea(['rows' => 5])->label(false) ?>
                </div>
            </div>
        <?php ActiveForm::end() ?>
    </div>
    <?php endforeach; ?>
</div>
