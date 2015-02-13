<?php

namespace OpenSearchServer\Tests\Index;

require_once __DIR__.'/../TestWrapperCreateIndex.php';
use OpenSearchServer\Tests\TestWrapperCreateIndex;

class DocumentTest extends TestWrapperCreateIndex
{
    public function testPutArray() {
        $request = new \OpenSearchServer\Document\Put();
        $request->index(self::$indexName);
        $request->addDocument(array(
            'lang' => \OpenSearchServer\Request::LANG_FR,
            'fields' => array(
                array(
                    'name' => 'url',
                    'value' => '1'
                ),
                array(
                    'name' => 'title',
                    'value' => 'The Count Of Monte-Cristo, Alexandre Dumas'
                ),
                array(
                    'name' => 'title',
                    'value' => 'Multiple value for field title'
                ),
                array(
                    'name' => 'autocomplete',
                    'value' => 'The Count Of Monte-Cristo, Alexandre Dumas'
                ),
                array(
                    'name' => 'content',
                    'value' => '"Very true," said Monte Cristo; "it is unnecessary, we know each other so well!"
        "On the contrary," said the count, "we know so little of each other."
        "Indeed?" said Monte Cristo, with the same indomitable coolness; "let us see. Are you not the soldier Fernand who deserted on the eve of the battle of Waterloo? Are you not the Lieutenant Fernand who served as guide and spy to the French army in Spain? Are you not the Captain Fernand who betrayed, sold, and murdered his benefactor, Ali? And have not all these Fernands, united, made Lieutenant-General, the Count of Morcerf, peer of France?"
        "Oh," cried the general, as if branded with a hot iron, "wretch,—to reproach me with my shame when about, perhaps, to kill me! No, I did not say I was a stranger to you.'
                ),
            )
        ));
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }
    
    public function testPutObjects() {
        $document = new \OpenSearchServer\Document\Document();
        $document->lang(\OpenSearchServer\Request::LANG_FR)
                 ->field('title','Test The Count 2')
                 ->field('title','One field can be indexed with multiple values')
                 ->field('autocomplete','Test The Count 2')
                 ->field('url', '2');
        
        $document2 = new \OpenSearchServer\Document\Document();
        $document2->lang(\OpenSearchServer\Request::LANG_FR)
                  ->field('title','Test The Count 3')
                  ->field('autocomplete','Test The Count 3')
                  ->field('url', '3');
        
        
        $request = new \OpenSearchServer\Document\Put();
        $request->index(self::$indexName)->addDocuments(array($document, $document2));
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }

    public function testPutText() {
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
        $this->assertTrue($response->isSuccess());
    }
    
    public function testDelete() {
        $request = new \OpenSearchServer\Document\Delete();
        $request->index(self::$indexName)
                ->field('url')
                ->value('3')
                ->values(array('4','5'));
        $response = self::$oss_api->submit($request);
        $this->assertEquals('3 document(s) deleted by url', $response->getInfo());
    }
    
    public function testDeleteQuery() {
        $request = new \OpenSearchServer\Document\DeleteByQuery();
        $request->index(self::$indexName)
                ->query('url:1 url:2 url:6');
        $response = self::$oss_api->submit($request);        
        $this->assertEquals('3 document(s) deleted', $response->getInfo());
    }
}