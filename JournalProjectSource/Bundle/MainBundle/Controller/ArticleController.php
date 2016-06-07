<?php

namespace Tribuca\Bundle\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Tribuca\Bundle\MainBundle\Entity\Article;
use Tribuca\Bundle\MainBundle\Entity\Keyword;

use Tribuca\Bundle\MainBundle\Form\ArticleType;

use Tribuca\Bundle\MainBundle\Entity\NewsPaper;
use Symfony\Component\HttpFoundation\Response;


/**
 * Article controller.
 * Gestion des articles et de l'indexation des mots clés
 */
class ArticleController extends Controller
{

    /**
     * Lists all Article entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TribucaMainBundle:Article')->findAll();

        return $this->render('TribucaMainBundle:Article:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    
    /**
     * Ajax called action : save an Article
     */
    public function ajax_saveAction()
    {    
        $request = $this->getRequest();
        $id = $request->get('article_id');
        if($request->isXmlHttpRequest())
        {  
            $em = $this->getDoctrine()->getManager();
            if($id != NULL && $id!=""){
                $entity = $em->getRepository('TribucaMainBundle:Article')->find($id);
            }
            else{
                $entity = new Article();
            }
            $form = $this->createCreateForm($entity);
            $form->handleRequest($request);

            if($form->isValid()){
               $em->persist($entity);
                //Recupere que ce qui est en post
                $keywords_str = $request->request->get('extra_fields');
                $existing_keywords = $entity->getKeywords();
                foreach ($existing_keywords as $keyword) {
                    $em->persist($entity->removeKeyword($keyword));
                }
                // $entity->removeAllKeywords();
                if(count($keywords_str) > 0){
                   foreach ($keywords_str as $keyword_str) {

                        $keyword = $this->getDoctrine()
                            ->getRepository('TribucaMainBundle:Keyword')
                            ->findOneByName($keyword_str); // la méthode find prend un ID, si tu veux récupérer un Keyword, via son attribut name, ce sera findOneByName($name)
                            

                        if($keyword == NULL){ // Si le keyword n'existe pas

                            $keyword = new Keyword();
                            $keyword -> setName($keyword_str); // ici, ce n'est pas $keyword mais $keyword_str
                            $em->persist($keyword); // on persist le keyword
                            $em->flush(); // on applique les changements en base de données
                        }
                        ;
                        $em->persist($entity->addKeyword($keyword));
                    } 
                }
                $em->flush();
                unset($form);
                $form = $this->createEditForm($entity);
                return $this->render('TribucaMainBundle:Article:create_success.html.twig', array('title' => $entity->getTitle(),
                                                                                                 'form' => $form->createView(),
                                                                                                 'newspaper' => $entity->getNewspaper(),
                                                                                                 'entity' => $entity,
                                                                                                 ));
            }

        }
        return $this->render('TribucaMainBundle:Article:main_form.html.twig', array('form' => $form->createView(),
                                                                                    'newspaper' => $entity->getNewspaper(),
                                                                                    'entity' => $entity,
                                                                                    ));
    }
    
    /**
     * Ajax called action : save an Article and call new action
     * @todo factoriser avec méthode ajax_saveAndContinueAction
     */
    public function ajax_saveAndContinueAction()
    {    
        $request = $this->getRequest();
        $id = $request->get('article_id');
        if($request->isXmlHttpRequest())
        {  
            $em = $this->getDoctrine()->getManager();
            if($id != NULL && $id!=""){
                $entity = $em->getRepository('TribucaMainBundle:Article')->find($id);
            }
            else{
                $entity = new Article();
            }
            $form = $this->createCreateForm($entity);
            $form->handleRequest($request);

            if($form->isValid()){
                $em->persist($entity);
                //Recupere que ce qui est en post
                $keywords_str = $request->request->get('extra_fields');
                $existing_keywords = $entity->getKeywords();
                foreach ($existing_keywords as $keyword) {
                    $em->persist($entity->removeKeyword($keyword));
                }
                // $entity->removeAllKeywords();
                if(count($keywords_str) > 0){
                   foreach ($keywords_str as $keyword_str) {

                        $keyword = $this->getDoctrine()
                            ->getRepository('TribucaMainBundle:Keyword')
                            ->findOneByName($keyword_str); // la méthode find prend un ID, si tu veux récupérer un Keyword, via son attribut name, ce sera findOneByName($name)
                            

                        if($keyword == NULL){ // Si le keyword n'existe pas

                            $keyword = new Keyword();
                            $keyword -> setName($keyword_str); // ici, ce n'est pas $keyword mais $keyword_str
                           
                            $em->persist($keyword); // on persist le keyword
                            $em->flush(); // on applique les changements en base de données

                        }
                        ;
                        if( $entity->getKeywords()->contains($keyword) === false )
                            $em->persist($entity->addKeyword($keyword));
                        else
                            $em->persist($entity);
                    } 
                }
                $em->flush();
                $title = $entity->getTitle();
                $newspaper = $entity->getNewspaper();
                // on prépare le create, puisque l'on est dans le save n continue
                $entity = new Article();
                $entity->setNewspaper($newspaper);
                $form = $this->createCreateForm($entity);

                return $this->render('TribucaMainBundle:Article:create_success.html.twig', array('title' => $title,
                                                                                                 'form' => $form->createView(),
                                                                                                 'newspaper' => $newspaper,
                                                                                                 'entity' => $entity,
                                                                                                 ));
            }
        }
        return $this->render('TribucaMainBundle:Article:main_form.html.twig', array('form' => $form->createView(),
                                                                                    'newspaper' => $entity->getNewspaper(),
                                                                                    ));
    }


	public function valid_form_forParseAction(){    
        $request = $this->getRequest();
        $id = $request->get('article_id');
        if($request->isXmlHttpRequest())
        {  
            $em = $this->getDoctrine()->getManager();
            if($id != NULL && $id!=""){
                $entity = $em->getRepository('TribucaMainBundle:Article')->find($id);
            }
            else{
                $entity = new Article();
            }
            $form = $this->createCreateForm($entity);
            $form->handleRequest($request);
        }
        return $this->render('TribucaMainBundle:Article:main_form.html.twig', array('form' => $form->createView(),
                                                                                    'newspaper' => $entity->getNewspaper(),
                                                                                    'entity' => $entity,
                                                                                    ));
    }    


    /**
     * Ajax called action : get keywords from an article content
     * render a template with list of keywords, with checkbox
     */
    public function ajax_parseAction(){
        $request = $this->container->get('request');

        if($request->isXmlHttpRequest())
        {
            $content = $request->get('content');
            $newspaper_id = $request->get('newspaper_id');
            $article_id = $request->get('article_id');
            $em = $this->getDoctrine()->getManager();
            $newspaper = $em->getRepository('TribucaMainBundle:NewsPaper')->find($newspaper_id);
            if($newspaper){
                $keywords = $this->getKeywordsList($content, $article_id);
            }
            else{
                throw $this->createNotFoundException('Unable to find NewsPaper entity.');
            }
            //@todo virer ces keywords lorsque getKeywordsAction fonctionnera
            
        }
        // @todo, this is a mock, change it by the correct call to pdf parse method
        return $this->render('TribucaMainBundle:Article:keyword_suggestions.html.twig',
                                array('suggested_keywords' => $keywords )
                            );
    }


    /**
     * Ajax called action : 
     * render a list of article, clickable
     */
    public function ajax_listAction(){
        $request = $this->container->get('request');

        if($request->isXmlHttpRequest())
        {
            $newspaper_id = $request->get('newspaper_id');
            $em  = $this->getDoctrine()->getManager();
            $newspaper = $em->getRepository('TribucaMainBundle:NewsPaper')->find($newspaper_id);
            return $this->render('TribucaMainBundle:Article:list.html.twig',
                                array('newspaper' => $newspaper )
                            );
        }
        else{
            throw $this->createNotFoundException('Wrong type of request.');
        }
    }

    /**
     * Ajax called action : render a list of keyword, un text inputs
     */
    public function ajax_listKeywordAction(){
        $request = $this->container->get('request');

        if($request->isXmlHttpRequest())
        {
            $article_id = $request->get('article_id');
            $em  = $this->getDoctrine()->getManager();
            $article = $em->getRepository('TribucaMainBundle:Article')->find($article_id);
            return $this->render('TribucaMainBundle:Article:keywordbox.html.twig',
                                array('article' => $article )
                            );
        }
        else{
            throw $this->createNotFoundException('Wrong type of request.');
        }
    }

    /**
     * Ajax called action : toggle IsFinished attribute for current newspaper
     */
    public function ajax_toggleIsFinishedAction(){
        $request = $this->container->get('request');

        if($request->isXmlHttpRequest())
        {
            $em  = $this->getDoctrine()->getManager();
            $is_finished = $request->get('is_finished');
            $newspaper_id = $request->get('newspaper_id');
            $newspaper = $em->getRepository('TribucaMainBundle:NewsPaper')->find($newspaper_id);
            
            //Passage de 0 à 1 (validation du journal terminé)
            //Rien ne doit empêcher la dévalidation
            if($is_finished == 0){
                $articles = $newspaper->getArticles();
                if(empty($articles))
                    return new Response(-1);

                foreach ($articles as $article) {
                    if( count($article->getKeywords()) === 0 )
                        return new Response(-1);
                }                
            }


            if($is_finished == 1){
                $newspaper->setIsFinished(false);
                $em->persist($newspaper);
                $em->flush();
                return new Response(0);
            }
            else{
                $newspaper->setIsFinished(true);
                $em->persist($newspaper);
                $em->flush();
                return new Response(1);
            }
        }
    }

    /**
     * Ajax called action : toggle "favorite" attribute for current keyword
     */
    public function ajax_toggleIsFavoriteAction(){
        $request = $this->container->get('request');

        if($request->isXmlHttpRequest())
        {
            $em  = $this->getDoctrine()->getManager();
            $is_favorite = $request->get('is_favorite');
            $name = $request->get('name');
            // $keyword_id = $request->get('keyword_id');
            $keyword = $em->getRepository('TribucaMainBundle:Keyword')->findOneByName($name);
            
            // $keyword_favs = $keyword->getfavorite();
            if(!$keyword){
                $keyword = new Keyword();
                $keyword->setName($name);
                $em->persist($keyword);
            }

            if($keyword->getFavorite()){
                $keyword->setFavorite(false);
                $em->persist($keyword);
                $em->flush();
                return new Response(0);
            }
            else{
                $keyword->setFavorite(true);
                $em->persist($keyword);
                $em->flush();
                return new Response(1);
            }
        }
    }

    /**
     * Return 1 if current keyword is favorite
     */
    public function ajax_isFavoriteAction(){
        $request = $this->container->get('request');

        if($request->isXmlHttpRequest())
        {
            $em  = $this->getDoctrine()->getManager();
            $name = $request->get('name');
            // $keyword_id = $request->get('keyword_id');
            $keyword = $em->getRepository('TribucaMainBundle:Keyword')->findOneByName($name);
            if($keyword && $keyword->getFavorite())
                return new Response(1);
            else
                return new Response(0);
        }
        return new Response(0);
    }

    /**
     * Creates a form to create a Article entity.
     *
     * @param Article $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Article $entity)
    {
        $form = $this->createForm(new ArticleType(), $entity, array(
            'action' => $this->generateUrl('article_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Article entity.
     *
     */
    public function newAction($newspaper_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$newspaper = $em->getRepository('TribucaMainBundle:NewsPaper')->find($newspaper_id);
    	if(!$newspaper) {
    		throw $this->createNotFoundException('Unable to find NewsPaper entity.');
    	}
    	// $pdf = $newspaper->getWebPath();
        $entity = new Article();
        $entity->setNewspaper($newspaper);
        $form   = $this->createCreateForm($entity);

        return $this->render('TribucaMainBundle:Article:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        	'newspaper' => $newspaper,
        ));
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TribucaMainBundle:Article')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $newspaper = $em->getRepository('TribucaMainBundle:Newspaper')->find($entity->getNewspaper());

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('TribucaMainBundle:Article:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'newspaper'   => $newspaper,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Article entity.
    *
    * @param Article $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Article $entity)
    {
        $form = $this->createForm(new ArticleType(), $entity, array(
            'action' => $this->generateUrl('article_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    
    /**
     * Deletes a Article entity.
     *
     */    
    public function ajax_deleteAction()
    {    
        $request = $this->getRequest();
        $id = $request->get('article_id');
        $newspaper_id = $request->get('newspaper_id');
        
        if($request->isXmlHttpRequest())
        {  
            $em = $this->getDoctrine()->getManager();

            if($id != NULL && $id!=""){
                $entity = $em->getRepository('TribucaMainBundle:Article')->find($id);
                if(!$entity) {
                    throw $this->createNotFoundException('Unable to find Article entity.');
                }
                // Supprimer l'article
                $em->remove($entity);
                $em->flush();
            }

            $entity = new Article();

            if($newspaper_id != NULL && $newspaper_id!=""){
                $newspaper = $em->getRepository('TribucaMainBundle:NewsPaper')->find($newspaper_id);
                if(!$newspaper) {
                    throw $this->createNotFoundException('Unable to find NewsPaper entity.');
                }
            }

            $entity->setNewspaper($newspaper);
            $form   = $this->createCreateForm($entity);
            return $this->render('TribucaMainBundle:Article:main_form.html.twig', array('form' => $form->createView(),
                                                                                    'newspaper' => $entity->getNewspaper(),
                                                                                    'entity' => $entity,
                                                                                    ));
            //$form->handleRequest($request);
        }

        return $this->redirect($this->generateUrl('article_new', array('newspaper_id' => $newspaper_id)));
    }

    /**
     * Creates a form to delete a Article entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('article_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Supprimer'))
            ->getForm()
        ;
    }
    
    
    /**
     * Propose 10 pertinent keywords from the extract article.
     */
    private function getKeywordsList($article, $article_id)
    {
        // @todo
              
        // creer une liste de mots non valides (dans une classe unvalid_words par exemple) : mots de moins de 3 lettres + verbes conjugués
        // creer un hash (clé-valeur) $words_list pour stocker les mots qui seront trouvés et leur occurence
        // parcourir l'extrait d'article qui a été stocké dans $article (et qui est une chaine de caracteres).
        // identifier chaque mot comme étant compris entre 2 espaces et le stocker dans une variable $word
        // vérifier si $word n'appartient pas à unvalid_words
        //      si non, verifier si $word est déjà dans $words_list
        //          si non, l'y mettre (clé du hash) et incrémenter sa valeur à 1
        //          si oui, incrémenter sa valeur dans le hash à +1
        //      si non, vérifier si $word appartient aux keywords (notre dico de mots favoris)
        //          si oui, incrémenter sa valeur dans le hash à +100 (pour être sûr qu'il passe en tête de liste)
        // refaire cet algo pour tous les mots de $article
        // trier le hash par ordre d'occurences decroissantes
        // mettre les 10 premiers mots du hash trié (donc ceux avec l'occurence la + forte) dans $keywordsList
               
        // => s'aider de http://dev.petitchevalroux.net/php/calculer-frequence-des-mots-texte-php.367.html
        
        // => dans cet algo, il manque à incrémenter :
        // +5 un mot qui serait dans le titre (difficulté : identifier le titre dans la chaine de caractère de $article)
        // +5 un mot qui serait dans le premier ou le dernier paragraphe
        
        // Tableaux des terminaisons invalides
        $invalid_suffix = array('aient');
        $favs = array();
        
        // Tableau des mots invalides
        $invalid_words  = array('elles', 'aussi', 'parmi', 'depuis') ;
            
        // Mise en format correct de l'article
        $article = str_ireplace("’", "'", $article);
        $article = str_ireplace(" - ", '', $article);
        $article = str_ireplace("...", " ... ", $article);
        $article = str_ireplace(".", " . ", $article);
        $article = str_ireplace(',',  ' , ', $article);
        $article = str_ireplace(':',  ' : ', $article);
        $article = str_ireplace(';',  ' ; ', $article);
        $article = str_ireplace('?',  ' ? ', $article);
        $article = str_ireplace('!',  ' ! ', $article);
        $article = str_ireplace('(',  ' ( ', $article);
        $article = str_ireplace(')',  ' ) ', $article);
        $article = str_ireplace("»", " » ", $article);
        $article = str_ireplace("«", " « ", $article);
        $article = str_ireplace("œ", "oe", $article);
        $article = str_ireplace("œ", "oe", $article);
        $article = str_replace(array("\r\n","\n")," ",$article); 
        $article = preg_replace('/\s{2,}/', ' ', $article);
        
        // Mettre to les mots en minuscules
        $article = strtolower($article);
        
        // Dictionnaire des mots avec leur poids 
        $mapWords = array();
        
        // Tableau des mots de l'article
        $tabWords = explode(" ", $article);
        
        // Compteur de la position des mots
        $i = 0;
        $max = count($tabWords);

        $em = $this->getDoctrine()->getManager();
        $arrKeyW = array();
        $entity = $em->getRepository('TribucaMainBundle:Article')->find($article_id);
        if($entity){
            $arrKeyW_objects = $entity->getKeywords();
            foreach ($arrKeyW_objects as $arrKeyW_object) {
                $arrKeyW[] = $arrKeyW_object->getName();
            }
        }

        // Créer la liste des mots avec leur poids
        foreach($tabWords as $w) {
            $fav = false;
            // Supprimer les espaces de début et de fin qui peuvent subsister
            $w = trim(strtolower($w));

            // Suppression des mots et des suffixes invalides  
            if(in_array($w, $invalid_words) || in_array($w, $invalid_suffix)) {
                continue;
            }
                  
            // Suppression des particules
            $w = str_ireplace("qu'",'', $w);
            if($w !== "aujourd'hui") $w = str_ireplace("d'", '', $w);
            $w = str_ireplace("c'", '', $w);
            $w = str_ireplace("l'", '', $w);
            $w = str_ireplace("s'", '', $w);

            if(in_array($w, $arrKeyW)) {
               continue;
            }

            // Enlève les mots de 4 lettres ou moins
            if(strlen($w) > 4) {

                // si mot clef favoris
                $keyword = $this->getDoctrine()
                        ->getRepository('TribucaMainBundle:Keyword')
                        ->findOneByName($w);
                if($keyword && $keyword->getFavorite()){
                    $favs[] = $w;
                    $fav = true;
                }
                // Si le mot existe, incrémenter son occurence
                if(isset($mapWords[$w])) {
                    if($fav)
                        $mapWords[$w] = $mapWords[$w] + 3;
                    else
                        $mapWords[$w]++;

                // Sinon l'ajouter
                } else {
                    // Si le mot est dans l'introduction ou la conclusion
                    if(($i < 20) || ($i > ($max - 20))) {
                        if($fav)
                            $mapWords[$w] = 6;
                        else
                            $mapWords[$w] = 2;
                    // Sinon
                    } else {
                        if($fav)
                            $mapWords[$w] = 3;
                        else
                            $mapWords[$w] = 1;
                    }
                }
            }

            $i++;
        }

        // Trier le tableau par poids
        arsort($mapWords);
            
        // Ne garder que les 10 premiers éléments
        $n = count($mapWords);
        for($i=10; $i<$n; $i++) {
            array_pop($mapWords);
        }
        
        $keywordsList = array();
        foreach($mapWords as $key => $value) {
            if(in_array($key, $favs)){
                $keywordsList[$key] = true;
            }
            else{
                $keywordsList[$key] = false;
            }
        }

        return $keywordsList;
    }
}