<?php

use Tracy\Debugger;

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

check_su_access(SU_RAW_SQL);

$textDomain = 'grotto_rawsql';

$params = [
    'textDomain' => $textDomain,
];

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

LotgdNavigation::superuserGrottoNav();

LotgdNavigation::addHeader('rawsql.category.execution');
LotgdNavigation::addNav('rawsql.nav.sql', 'rawsql.php');
LotgdNavigation::addNav('rawsql.nav.php', 'rawsql.php?op=php');

$op = (string) LotgdRequest::getQuery('op');

if ('' == $op || 'sql' == $op)
{
    $params['tpl'] = 'default';

    $sql = (string) LotgdRequest::getPost('sql');

    if ('' != $sql)
    {
        $params['isResult'] = true;

        $params['sql'] = $sql;

        LotgdResponse::pageDebug('Ran Raw SQL: '.$sql);

        try
        {
            $q = Doctrine::getConnection()->prepare($sql);
            $q->execute();

            $params['rowCount'] = $q->rowCount(); //-- Count affected rows

            if ($q->columnCount())
            {
                $params['resultSql'] = $q->fetchAll(); //-- Select results if have columns
            }
        }
        catch (Throwable $th)
        {
            $params['resultSql'] = null;
            LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.sql.error.th', ['error' => $th->getMessage()], $textDomain));
            Debugger::log($th);
        }
    }
}
else
{
    $params['tpl'] = 'php';

    $php = stripslashes((string) LotgdRequest::getPost('php'));

    if ($php > '')
    {
        $params['php']       = $php;
        $params['highlight'] = highlight_string("<?php\n{$php}\n?>", true);

        try
        {
            ob_start();
            eval($php);
            $params['result'] = ob_get_contents();
            ob_end_clean();
        }
        catch (Exception $ex)
        {
            LotgdFlashMessages::addErrorMessage(LotgdTranslator::t('flash.message.php.error.th', ['error' => $ex->getMessage()], $textDomain));
        }
        LotgdLog::debug('Ran Raw PHP: '.$php);
    }
}

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/rawsql.html.twig', $params));

//-- Finalize page
LotgdResponse::pageEnd();
