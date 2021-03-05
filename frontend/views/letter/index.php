<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<div id="bloque-margaret">
    <div id="margaret">
        <div id="foto-margaret">
            <?= Html::img('@web/images/margaret.png') ?>
        </div>
        <div id="datos-margaret">
            <h1>MARGARET LUND PHD</h1>
            <div id="title">Bachelor, Masters and Doctorate degrees in Education</div>
            <div id="lifelong">
                A lifelong vocation – working with candidates of all ages for 40
                years to improve language skills and attain great qualifications.
            </div>
        </div>
    </div>
</div>

<div id="carta">
    <div class="container">
        <h2>Welcome to the Oxford Tutorial College Certificate!</h2>
        <div>
            We have designed the Oxford Tutorial College Certificate (Oxford TCC) with the
            goal of giving English learners access to a certified qualification of achievement
            and participation in the English language, which would allow them to discover their
            abilities in a second language. Our exam materials are created by an academic team
            from London, supported by regional consultants. The exam is administered and marked
            by native speaking professionals only. The Common European Framework of Reference
            for Languages (CEFR) is the guideline we use to assess the achievements of English
            language learners, as it is an internationally recognised standard.
        </div>
        <div>
            In accordance with this guideline, the Oxford TCC identifies the language skills
            that the learner currently has. All grades are split into three main levels,
            starting with basic users, which includes A1 beginner level and A2 upper basic level.
            It then continues through to learners becoming independent users, including a B1 intermediate
            level and a B2 upper intermediate level. Finally, there is the proficient level, which
            consists of C1 advanced level, and C2, which is mastery. The Oxford TCC assesses all areas of
            language learning, including reading and listening comprehension, use of English, speaking and writing.
        </div>
        <div>
            Each candidate will receive an Oxford Tutorial College Certificate according to the results of their exam.
            This not only gives them a tool to measure their English language level,
            but also a competitive advantage over their peers. Whether they are in school, university or the
            workplace, we hope that candidates will find this a very useful certification, as it provides them with
            an additional level of prestige and proof of linguistic ability.
        </div>
        <div>
            Remember that having an international English certificate is no longer a plus, it’s a MUST!
        </div>
        <div><span>Warm regards,</span></div>
        <div><span>Margaret Lund PhD</span></div>
        <?= Html::img('@web/images/firma.png') ?>
        <div><span>Oxford Education Academic Director</span></div>
    </div>
</div>

<?= $this->render('_profile', [
    'model' => $model,
    'institutoForm' => $institutoForm,
    'passwordForm' => $passwordForm,
    'paises'=>$paises,
]) ?>
