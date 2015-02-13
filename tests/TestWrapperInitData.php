<?php

namespace OpenSearchServer\Tests;

require_once __DIR__.'/TestWrapperCreateIndex.php';
use OpenSearchServer\Tests\TestWrapperCreateIndex;

class TestWrapperInitData extends TestWrapperCreateIndex
{
    //Add data to the index
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        
        $data = <<<TEXT
4;The Three Musketeers;In 1625 France, d'Artagnan-a poor young nobleman-leaves his family in Gascony and travels to Paris with the intention of joining the Musketeers of the Guard. However, en route, at an inn in Meung-sur-Loire, an older man derides d'Artagnan's horse and, feeling insulted, d'Artagnan demands to fight a duel with him. The older man's companions beat d'Artagnan unconscious with a pot and a metal tong that breaks his sword. His letter of introduction to Monsieur de Tréville, the commander of the Musketeers, is stolen. D'Artagnan resolves to avenge himself upon the man, who is later revealed to be the Comte de Rochefort, an agent of Cardinal Richelieu, who is in Meung to pass orders from the Cardinal to Milady de Winter, another of his agents.;en
5;Twenty Years After;The action begins under Queen Anne of Austria regency and Cardinal Mazarin ruling. D'Artagnan, who seemed to have a promising career ahead of him at the end of The Three Musketeers, has for twenty years remained a lieutenant in the Musketeers, and seems unlikely to progress, despite his ambition and the debt the queen owes him;en
6;The Vicomte de Bragelonne;The principal heroes of the novel are the musketeers. The novel's length finds it frequently broken into smaller parts. The narrative is set between 1660 and 1667 against the background of the transformation of Louis XIV from child monarch to Sun King.;en";
TEXT;
        $request = new \OpenSearchServer\Document\PutText();
        $request->index(self::$indexName)
                ->pattern('(.*?);(.*?);(.*?);(.*?)')
                ->data($data)
                ->langpos(4)
                ->buffersize(100)
                ->charset('UTF-8')
                ->fields(array('url', 'title', 'content', 'lang'));
        $response = self::$oss_api->submit($request);
    }
}