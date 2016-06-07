<?php
/**
 * file : NewsPaperController.php 
 *
 * Gestion des journaux
 */

/**
 * Espaces de noms
 */
namespace Tribuca\Bundle\MainBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tribuca\Bundle\MainBundle\Entity\NewsPaper;
use Tribuca\Bundle\MainBundle\Form\NewsPaperType;

/**
 * NewsPaper controller.
 * Gestion des journaux
 */
class NewsPaperController extends Controller
{
    /**
     * Liste tous les journaux indexés
     * @param  object  [$entity       = null]                       Entité journal
     * @param  object  [$form         = null]                       Formulaire préremplit à afficher dans le modal
     * @param  boolean [$showModalNew = false]                      Afficher le modal au chargement de la page
     * @param  string  [$modaltitle   = "Ajouter un journal"]       Titre du modal
     * @param  string  [$formpath     = "newspaper_create"]         Chemin du formulaire
     * @return string  Vue index.html.twig
     */
    public function indexAction($entity = null, $form = null, $showModalNew = false, $modaltitle = "Ajouter un journal", $formpath = "newspaper_create")
    {
        // Initialisation du manager d'entités
        $em = $this->getDoctrine()->getManager();

        // Récupère tous les journaux indexés
        $entities = $em->getRepository('TribucaMainBundle:NewsPaper')->findAll();

        // Création des formulaires de suppression
        $deleteForms = array();
        foreach($entities as $e) {
            $deleteForm = $this->createDeleteForm($e->getId());
            $deleteForms[$e->getId()] = $deleteForm->createView();
        }

        // Affiche la vue
        if(empty($form)) {
            return $this->render('TribucaMainBundle:NewsPaper:index.html.twig', array(
                'entities'      => $entities,
                'modaltitle'    => $modaltitle,
                'showModalNew'  => $showModalNew,
                'delete_form'   => $deleteForms,
                'form_path'     => $formpath,
            ));
        }
        // Afficher la vue avec le formulaire d'ajout ou d'édition préremplit ainsi que les messages d'erreurs
        else {
            return $this->render('TribucaMainBundle:NewsPaper:index.html.twig', array(
                'entities'      => $entities,
                'modaltitle'    => $modaltitle,
                'entity'        => $entity,
                'form'          => $form->createView(),
                'showModalNew'  => $showModalNew,
                'delete_form'   => $deleteForms,
                'form_path'     => $formpath,
            ));
        }

    }

    /**
     * Affiche le formulaire d'indexation d'un journal
     * @param  integer $form_path           Chemin du formulaire
     * @param  string  $modaltitle          Titre du modal
     * @param  object  [$entity    = null]  Entité journal
     * @param  object  [$form      = null]  Formulaire préremplit à afficher dans le modal
     * @return string  Vue Affiche le modal
     */
    public function newAction($form_path, $modaltitle, $entity = null, $form = null)
    {
        // Création d'un nouveau journal
        if(empty($entity)) {
            $entity = new NewsPaper();

            // Création du formulaire
            $form   = $this->createCreateForm($entity);

            //Afficher un formulaire vide
            return $this->render('TribucaMainBundle:NewsPaper:new.html.twig', array(
                'entity'        => $entity,
                'modaltitle'    => $modaltitle,
                'form'          => $form->createView(),
                'form_path'     => $form_path,
            ));
        }
        // Afficher le formulaire préremplie avec les messages d'erreurs
        else {
            return $this->render('TribucaMainBundle:NewsPaper:new.html.twig', array(
                'entity'        => $entity,
                'modaltitle'    => $modaltitle,
                'form'          => $form,
                'form_path'     => $form_path,
            ));
        }
    }

    /**
     * Création d'un journal
     * @param  array Request $request Entité journal passé en POST
     */
    public function createAction(Request $request)
    {
        // Création de l'entité
        $entity = new NewsPaper();

        // Création du formulaire
        $form = $this->createCreateForm($entity);

        // Vérification des données
        $form->handleRequest($request);

        // Si les données sont valides
        if ($form->isValid()) {
            // Enregistre le journal en base
            // @todo récupérer le fichier passé en attribut "path", le stocker
            // valoriser l'attribut path de l'entité avec le chemin du fichierjjj
            $em = $this->getDoctrine()->getManager();
            
            // Avant d'upload le fichier vérifier si l'ancien est présent
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            // Retour à la liste des journaux
            return $this->redirect($this->generateUrl('newspaper'));
        }



        // Affiche le modal avec les messages d'erreurs
        return $this->indexAction($entity, $form, true);
    }

    /**
     * Affiche un journal détaillé
     * TODO: A SUPPRIMER
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TribucaMainBundle:NewsPaper')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsPaper entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('TribucaMainBundle:NewsPaper:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Affiche le formulaire de modification préremplit
     * @param  integer $id Identifiant du journal à modifier
     * @return string  Vue du modal préremplit
     */
    public function editAction($id)
    {
        // Initialisation du manager d'entités
        $em = $this->getDoctrine()->getManager();

        // Récupération de l'entité journal
        $entity = $em->getRepository('TribucaMainBundle:NewsPaper')->find($id);

        //Si il n'y a pas d'entité afficher un message d'erreur
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsPaper entity.');
        }

        // Création du formulaire préremplit
        $editForm = $this->createEditForm($entity);

        // Afficher la vue avec le formulaire d'édition préremplit
        return $this->indexAction($entity, $editForm, true, "Éditer un journal", "newspaper_update");
    }

    /**
     * Mise à jour du journal
     * @param  array   Request $request donnés POST du journal modifié
     * @param  integer $id     Identifiant du journal
     * @return string  Vue du modal préremplit avec les messages d'erreurs ou liste des journaux
     */
    public function updateAction(Request $request, $id)
    {
        // Initialisation du manager d'entités
        $em = $this->getDoctrine()->getManager();

        // Récupération de l'entité journal
        $entity = $em->getRepository('TribucaMainBundle:NewsPaper')->find($id);

        //Si il n'y a pas d'entité afficher un message d'erreur
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsPaper entity.');
        }

        // Création du formulaire préremplit
        $editForm = $this->createEditForm($entity);  
        $editForm->handleRequest($request);
        
        // Si les données sont valides
        if ($editForm->isValid()) {
        	$article_destination = $request->get('article_destination');
            if($request->files->get('tribuca_bundle_mainbundle_newspaper')['file'] != NULL) {
                $uploadedFile = $request->files->get('tribuca_bundle_mainbundle_newspaper')['file']->getClientOriginalName();
                if($entity->getPath() != $uploadedFile && file_exists($entity->getAbsolutePath())) {
                    // Suppression de l'ancien journal
                    unlink($entity->getAbsolutePath());
                }
            }
            
            // Enregistre le journal en base
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            
            if($request->request->has('article_destination')) {
            	return $this->redirect($this->generateUrl('article_new',  array('newspaper_id' => $entity->getId())));
            }
            else {
            	
            // Retour à la liste des journaux
            	return $this->redirect($this->generateUrl('newspaper'));
            }
            
        }
        // Affiche le modal avec les messages d'erreurs
        return $this->indexAction($entity, $editForm, true, "Éditer un journal", "newspaper_update");
    }

    /**
     * Supprimer un journal
     * @param  array   Request $request donnés POST du journal modifié
     * @param  integer $id     Identifiant du journal
     * @return string  Liste des journaux
     */
    public function deleteAction(Request $request, $id)
    {
        // Création du formulaire
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        // Si les données sont valides
        if ($form->isValid()) {
            // Initialisation du manager d'entités
            $em = $this->getDoctrine()->getManager();

            // Récupération de l'entité journal
            $entity = $em->getRepository('TribucaMainBundle:NewsPaper')->find($id);

            //Si il n'y a pas d'entité afficher un message d'erreur
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find NewsPaper entity.');
            }
            
            // Supprimer le journal
            $em->remove($entity);
            $em->flush();
        }
        // Retour à la liste des journaux
        return $this->redirect($this->generateUrl('newspaper'));
    }

    /**
    * Creates a form to edit a NewsPaper entity.
    *
    * @param NewsPaper $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(NewsPaper $entity)
    {
        $form = $this->createForm(new NewsPaperType(), $entity, array(
            'action' => $this->generateUrl('newspaper_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'validation_groups' => array('Edit'),
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Creates a form to delete a NewsPaper entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('newspaper_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Supprimer'))
            ->getForm()
            ;
    }


    /**
     * Création du formulaire d'indexation d'un journal
     * @param NewsPaper $entity L'entité
     * @return \Symfony\Component\Form\Form Le formulaire
     */
    private function createCreateForm(NewsPaper $entity)
    {
        // Création du formulaire en fonction de l'entité
        $form = $this->createForm(new NewsPaperType(), $entity, array(
            'action' => $this->generateUrl('newspaper_create'),
            'method' => 'POST',
             'validation_groups' => array('New'),
        ));

        // Ajout d'un bouton de validation
        $form->add('submit', 'submit', array('label' => 'Create'));

        // Retourne le formulaire
        return $form;
    }

     /*Cette action doit récupérer la liste des articles liés au journal en cours, et la renvoyer à la vue.*/

     //click bouton Journal terminé
     public function clickButton(){

        $recup = getButton();

        if($recup == false)
        {
            $em->persist($newspaper);
            setButton(true);
        }
        else
        {
            $em->persist($newspaper);
            setButton(false);
        }
    }
}
