<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

tlschema('rawsql');

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
        /**
         * For now use zend-db
         *
         * @TODO Script to create SQL in Doctrine
         */
        $sql = stripslashes($sql);
        $params['sql'] = $sql;

        debuglog('Ran Raw SQL: '.$sql);

        try
        {
            DB::query($sql, false);

            $error = DB::errorInfo();

            if ($error)
            {
                \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.sql.error.db', [ 'error' => $error ], $textDomain));
            }
        }
        catch (\Throwable $th)
        {
            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.sql.error.th', [ 'error' => $ex->getMessage() ], $textDomain));
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
