<?php
use yii\helpers\Html;
?>

<div id="levels">
    <div class="container">
        <h1>LEVELS</h1>
        <div class="user-type">
            <h2>BASIC USER</h2>
            <div class="level-info" id="basic-a1">
                <div class="level">
                    A1
                </div>
                <div class="info">
                    Can understand and use familiar everyday expressions and very basic
                    phrases aimed at the satisfaction of needs of a concrete type.
                    Can introduce him/herself and others and can ask and answer
                    questions about personal details such as where he/she lives,
                    people he/she knows and things he/she has. Can interact in a simple way,
                    provided the other person talks slowly and clearly and is prepared to help.
                </div>
            </div>
            <div class="level-info" id="basic-a2">
                <div class="level">
                    A2
                </div>
                <div class="info">
                    Can understand sentences and frequently used expressions related
                    to areas of most immediate relevance (e.g very basic personal and
                    family information, shopping, local geography, employment).
                    Can communicate in simple and routine tasks requiring a simple and direct
                    exchange of information on familiar and routine matters.
                    Can describe in simple terms aspects of his/her background, immediate
                    environment and matters in areas of immediate need.
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="user-type">
            <h2>INDEPENDENT USER</h2>
            <div class="level-info" id="independent-b1">
                <div class="level">
                    B1
                </div>
                <div class="info">
                    Can understand the main points of clear standard input on familiar
                    matters regularly encountered in work, school, leisure etc.
                    Can deal with most situations likely to arise whilst travelling
                    in an area where the language is spoken. Can produce simple connected
                    text on topics which are familiar or of personal interest.
                    Can describe experiences and events, dreams, hopes and ambitions,
                    and briefly give reasons and explanations for opinions and plans.
                </div>
            </div>
            <div class="level-info" id="independent-b2">
                <div class="level">
                    B2
                </div>
                <div class="info">
                    Can understand the main ideas of complex text on both concrete
                    and abstract topics, including technical discussions in his/her
                    field of specialisation. Can interact with a degree of fluency
                    and spontaneity that makes regular interaction with native
                    speakers quite possible without strain for either party.
                    Can produce clear, detailed text on a wide range of subjects
                    and explain a viewpoint on a topical issue giving the advantages
                    and disadvantages of various options.
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="user-type">
            <h2>PROFICIENT USER</h2>
            <div class="level-info" id="proficient-c1">
                <div class="level">
                    C1
                </div>
                <div class="info">
                    Can understand a wide range of demanding, longer texts, and
                    recognise implicit meaning. Can express him/herself fluently
                    and spontaneously without much obvious searching for expressions.
                    Can use language flexibly and effectively for social, academic
                    and professional purposes. Can produce clear, well-structured,
                    detailed text on complex subjects, showing controlled use of
                    organisational patterns, connectors and cohesive devices.
                </div>
            </div>
            <div class="level-info" id="proficient-c2">
                <div class="level">
                    C2
                </div>
                <div class="info">
                    Can understand with ease virtually everything heard or read.
                    Can summarise information from different spoken and written
                    sources, reconstructing arguments and accounts in a coherent
                    presentation. Can express him/ herself spontaneously very
                    fluently and precisely, differentiating finer shades of meaning
                    even in more complex situations.
                </div>
            </div>
            <div class="clear"></div>
        </div>

    </div>
    <div id="councils" class="container">
        Council of Europe:
        <a href="https://www.coe.int/en/web/common-european-framework-reference-languages" target="_blank">
            https://www.coe.int/en/web/common-european-framework-reference-languages
        </a>
    </div>

    <div class="container">
        <a href="#chart" class="popup-with-zoom-anim" id="level-comparison">
            OXFORD TCC LEVEL COMPARISON CHART
        </a>
    </div>
</div>

<div id="chart" class="mfp-hide white-popup-block popup-exam">
    <h2>OXFORD TCC LEVEL COMPARISON CHART</h2>
    <?= Html::img('@web/images/levels.jpg') ?>
</div>

<?= $this->render('_profile', [
    'model' => $model,
    'institutoForm' => $institutoForm,
    'passwordForm' => $passwordForm,
    'paises'=>$paises,
]) ?>
