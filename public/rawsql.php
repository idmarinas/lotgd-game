<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

check_su_access(SU_RAW_SQL);

$textDomain = 'page-rawsql';

$params = [
    'textDomain' => $textDomain
];

page_header('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addHeader('rawsql.category.execution');
\LotgdNavigation::addNav('rawsql.nav.sql', 'rawsql.php');
\LotgdNavigation::addNav('rawsql.nav.php', 'rawsql.php?op=php');

$op = (string) \LotgdHttp::getQuery('op');

if ('' == $op || 'sql' == $op)
{
    $params['tpl'] = 'default';

    $sql = (string) \LotgdHttp::getPost('sql');

    if ('' != $sql)
    {
        $params['isResult'] = true;

        $params['sql'] = $sql;

        debug('Ran Raw SQL: '.$sql);

        try
        {
            $q = \Doctrine::getConnection()->prepare($sql);
            $q->execute();

            $params['rowCount'] = $q->rowCount(); //-- Count affected rows

            if ($q->columnCount())
            {
                $params['resultSql'] = $q->fetchAll(); //-- Select results if have columns
            }
        }
        catch (\Throwable $th)
        {
            $params['resultSql'] = null;
            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.sql.error.th', [ 'error' => $th->getMessage() ], $textDomain));
            \Tracy\Debugger::log($th);
        }
    }
}
else
{
    $params['tpl'] = 'php';

    $php = stripslashes((string) \LotgdHttp::getPost('php'));

    if ($php > '')
    {
        $params['php'] = $php;
        $params['highlight'] = highlight_string("<?php\n$php\n?>", true);

        try
        {
            ob_start();
            eval($php);
            $params['result'] = ob_get_contents();
            ob_end_clean();
        }
        catch (Exception $ex)
        {
            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.php.error.th', [ 'error' => $ex->getMessage() ], $textDomain));
        }
        debuglog('Ran Raw PHP: '.$php);
    }
}

rawoutput(\LotgdTheme::renderLotgdTemplate('core/page/rawsql.twig', $params));

page_footer();
