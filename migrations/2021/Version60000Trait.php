<?php

declare(strict_types=1);

namespace DoctrineMigrations;

trait Version60000Trait
{

    private function importData()
    {

        //-- Import characters to avatar
        $this->addSql('INSERT INTO avatar
        (`id`, `acct_id`, `name`, `playername`, `sex`, `strength`, `dexterity`, `intelligence`, `constitution`, `wisdom`, `specialty`, `experience`, `gold`, `weapon`, `armor`, `seenmaster`, `level`, `defense`, `attack`, `alive`, `goldinbank`, `marriedto`, `spirits`, `hitpoints`, `maxhitpoints`, `permahitpoints`, `gems`, `weaponvalue`, `armorvalue`, `location`, `turns`, `title`, `badguy`, `companions`, `allowednavs`, `resurrections`, `weapondmg`, `armordef`, `age`, `charm`, `specialinc`, `specialmisc`, `lastmotd`, `playerfights`, `lasthit`, `seendragon`, `dragonkills`, `restorepage`, `hashorse`, `bufflist`, `dragonpoints`, `boughtroomtoday`, `sentnotice`, `pvpflag`, `transferredtoday`, `soulpoints`, `gravefights`, `hauntedby`, `deathpower`, `recentcomments`, `bio`, `race`, `biotime`, `amountouttoday`, `pk`, `dragonage`, `bestdragonage`, `ctitle`, `slaydragon`, `fedmount`, `clanid`, `clanrank`, `clanjoindate`, `chatloc`)
        SELECT `id`, `acct_id`, `name`, `playername`, `sex`, `strength`, `dexterity`, `intelligence`, `constitution`, `wisdom`, `specialty`, `experience`, `gold`, `weapon`, `armor`, `seenmaster`, `level`, `defense`, `attack`, `alive`, `goldinbank`, `marriedto`, `spirits`, `hitpoints`, `maxhitpoints`, `permahitpoints`, `gems`, `weaponvalue`, `armorvalue`, `location`, `turns`, `title`, `badguy`, `companions`, `allowednavs`, `resurrections`, `weapondmg`, `armordef`, `age`, `charm`, `specialinc`, `specialmisc`, `lastmotd`, `playerfights`, `lasthit`, `seendragon`, `dragonkills`, `restorepage`, `hashorse`, `bufflist`, `dragonpoints`, `boughtroomtoday`, `sentnotice`, `pvpflag`, `transferredtoday`, `soulpoints`, `gravefights`, `hauntedby`, `deathpower`, `recentcomments`, `bio`, `race`, `biotime`, `amountouttoday`, `pk`, `dragonage`, `bestdragonage`, `ctitle`, `slaydragon`, `fedmount`, `clanid`, `clanrank`, `clanjoindate`, `chatloc` FROM `characters`');

        //-- Import accounts to user
        $this->addSql('INSERT INTO  user (`acctid`, `avatar_id`, `laston`, `loggedin`, `superuser`, `login`, `lastmotd`, `locked`, `lastip`, `uniqueid`, `boughtroomtoday`, `emailaddress`, `replaceemail`, `emailvalidation`, `sentnotice`, `prefs`, `transferredtoday`, `recentcomments`, `amountouttoday`, `regdate`, `banoverride`, `donation`, `donationspent`, `donationconfig`, `referer`, `refererawarded`, `password`, `forgottenpassword`)
        SELECT `acctid`, `character_id`, `laston`, `loggedin`, `superuser`, `login`, `lastmotd`, `locked`, `lastip`, `uniqueid`, `boughtroomtoday`, `emailaddress`, `replaceemail`, `emailvalidation`, `sentnotice`, `prefs`, `transferredtoday`, `recentcomments`, `amountouttoday`, `regdate`, `banoverride`, `donation`, `donationspent`, `donationconfig`, `referer`, `refererawarded`, `password`, `forgottenpassword` FROM accounts');
    }
}
