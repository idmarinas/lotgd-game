<?php

require_once 'common.php';
require_once 'lib/showform.php';
require_once 'lib/datetime.php';
require_once 'lib/sanitize.php';
require_once 'lib/names.php';

check_su_access(SU_EDIT_BANS);

$textDomain = 'page-bans';
$params = [ 'textDomain' => $textDomain ];

$op = \LotgdHttp::getQuery('op');
$userId = (int) \LotgdHttp::getQuery('userid');

page_header('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();
\LotgdNavigation::addHeader('bans.category.bans');
\LotgdNavigation::addNav('bans.nav.default', 'bans.php');
\LotgdNavigation::addNav('bans.nav.add', 'bans.php?op=setupban');
\LotgdNavigation::addNav('bans.nav.list', 'bans.php?op=removeban');
\LotgdNavigation::addNav('bans.nav.search', 'bans.php?op=searchban');

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);

switch ($op)
{
    case 'setupban':
        $params['opt'] = 'setupban';

        $params['account'] = $repository->getBasicInfoOfAccount($userId);

        $params['equalId'] = $repository->getAccountsWithEqualId($params['account']['uniqueid']);
        $params['similarIp'] = $repository->getAccountsWithSimilarIp($params['account']['lastip'], $params['account']['acctid']);
    break;
    case 'saveban':
        $params['opt'] = 'saveban';

        require_once 'lib/bans/case_saveban.php';
    break;
    case 'searchban':
        $params['searchBan'] = true;

        $target = (string) \LotgdHttp::getPost('target');

    case 'removeban':
    case 'delban':
        $params['opt'] = 'removeban';

        require_once 'lib/bans/case_removeban.php';
    break;
    case 'search':
    default:
        $params['opt'] = 'default';

        $repoAcctEveryPage = \Doctrine::getRepository(\Lotgd\Core\Entity\AccountsEverypage::class);
        $page = (int) \LotgdHttp::getQuery('page');
        $sort = (string) \LotgdHttp::getQuery('sort');
        $order = (string) ($sort ?: 'acctid');

        $query = (string) \LotgdHttp::getPost('q');
        $query = (string) ($query ?: \LotgdHttp::getQuery('q'));

        $params['query'] = $query ? "q={$query}" : '';
        $params['paginator'] = $repository->bansSearchAccts($query, $order, $page);
        $params['stats'] = $repoAcctEveryPage->getStatsPageGen();

        $params['paginatorLink'] = \LotgdHttp::getServer('REQUEST_URI');
    break;
}

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/bans.twig', $params));

page_footer();
