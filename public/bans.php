<?php

require_once 'common.php';
require_once 'lib/showform.php';
require_once 'lib/names.php';

check_su_access(SU_EDIT_BANS);

$textDomain = 'page_bans';
$params = [ 'textDomain' => $textDomain ];

$op = \LotgdRequest::getQuery('op');
$userId = (int) \LotgdRequest::getQuery('userid');

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();
\LotgdNavigation::addHeader('bans.category.bans');
\LotgdNavigation::addNav('bans.nav.default', 'bans.php');
\LotgdNavigation::addNav('bans.nav.add', 'bans.php?op=setupban');
\LotgdNavigation::addNav('bans.nav.list', 'bans.php?op=removeban');
\LotgdNavigation::addNav('bans.nav.search', 'bans.php?op=searchban');

$repository = \Doctrine::getRepository('LotgdCore:User');

switch ($op)
{
    case 'setupban':
        $params['opt'] = 'setupban';

        $params['account'] = $repository->getBasicInfoOfAccount($userId);

        $params['equalId'] = $repository->getAccountsWithEqualId((string) $params['account']['uniqueid']);
        $params['similarIp'] = $repository->getAccountsWithSimilarIp($params['account']['lastip'], $params['account']['acctid']);
    break;
    case 'saveban':
        $params['opt'] = 'saveban';

        require_once 'lib/bans/case_saveban.php';
    break;
    case 'searchban':
        $params['searchBan'] = true;

        $target = (string) \LotgdRequest::getPost('target');

    case 'removeban':
    case 'delban':
        $params['opt'] = 'removeban';

        require_once 'lib/bans/case_removeban.php';
    break;
    case 'search':
    default:
        $params['opt'] = 'default';

        $repoAcctEveryPage = \Doctrine::getRepository(\Lotgd\Core\Entity\AccountsEverypage::class);
        $page = (int) \LotgdRequest::getQuery('page');
        $sort = (string) \LotgdRequest::getQuery('sort');
        $order = $sort ?: 'acctid';

        $query = (string) \LotgdRequest::getPost('q');
        $query = (string) ($query ?: \LotgdRequest::getQuery('q'));

        $params['query'] = $query !== '' && $query !== '0' ? "q={$query}" : '';
        $params['paginator'] = $repository->bansSearchAccts($query, $order, $page);
        $params['stats'] = $repoAcctEveryPage->getStatsPageGen();

        $params['paginatorLink'] = \LotgdRequest::getServer('REQUEST_URI');
    break;
}

\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/bans.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
