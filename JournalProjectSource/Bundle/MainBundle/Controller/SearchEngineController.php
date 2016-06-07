<?php

namespace Tribuca\Bundle\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Tribuca\Bundle\MainBundle\Entity\Article;
use Tribuca\Bundle\MainBundle\Entity\ArticleRepository;

use Symfony\Component\HttpFoundation\Response;


/**
 * Article controller.
 *
 */
class SearchEngineController extends Controller
{
	public function searchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        // get keywords
        $keywords = explode(" ", $request->get('keywords'));
        $query = $em->getRepository('TribucaMainBundle:Article')->createQueryBuilder('a')
        														->addSelect('COUNT(k.id) as nkeyword')
        														->join('a.keywords', 'k')
		    													->join('a.newspaper', 'n');
        if (!empty($keywords)) {
            $i = 0;
            foreach ($keywords as $keyword) {
                $query->orWhere('k.name LIKE :keyword'.$i)->setParameter('keyword'.$i, '%' . $keyword . '%')->orWhere('a.title LIKE :keyword'.$i)->setParameter('keyword'.$i, '%' . $keyword . '%');
                $i++;
            }
        }

        // get filters : date, num and author
		$month = $request->get('month');
		$year = $request->get('year');
		$author = $request->get('author');
		$numero = $request->get('numero');
		$orderBy = $request->get('orderBy');


		// Date management
		$date_from = NULL;
		$date_to = NULL;
		if($year){
			$date_from = new \DateTime($year."-01-01 00:00:00");
			$date_to = new \DateTime(($year+1)."-01-01 00:00:00");
		}
		if($month && $year){
			$date_from = new \DateTime($year."-".$month."-01 00:00:00");
			$date_to = clone $date_from;
			$date_to->add(new \DateInterval("P1M"));
		}
		if ($date_from && $date_to) {
            $query->andWhere("n.publicationDate >= :date_from")->setParameter('date_from', $date_from);
            $query->andWhere("n.publicationDate <= :date_to")->setParameter('date_to', $date_to);
        }
        if($author){
        	$query->andWhere('a.author LIKE :author')->setParameter('author', '%' . $author . '%');
        }
        if($numero){
        	$query->andWhere('n.number = :numero')->setParameter('numero', $numero);
        }


        switch ($orderBy) {
        	case 'Pertinence':
        		$query->groupBy('a.id')
			 		  ->orderBy("nkeyword", 'DESC');
        		break;
        	case 'Date':
        		$query->groupBy('a.id')
			 		  ->orderBy("n.publicationDate", 'DESC');
        		break;
        	case 'Auteur':
        		$query->groupBy('a.id')
			 		  ->orderBy("a.author", 'ASC');
        		break;
        	
        	default:
        		$query->groupBy('a.id')
			 		  ->orderBy("nkeyword", 'DESC');
        		break;
        }
		

        // Get results :
        $results = $query->getQuery()->getResult();
		// var_dump($query->getQuery()->getDQL());die();
		$return = array();
		foreach ($results as $result) {
			$article = $result[0];
			$return[] = array(
					'title'     => $article->getTitle(),
					'page'      => $article->getPage(),
                    'author'    => $article->getAuthor(),
					'link'      => $article->getNewspaper()->getWebPath(),
					'numero'    => $article->getNewspaper()->getNumber(),
					'date'      => $article->getNewspaper()->getPublicationDate()->format('d-m-Y'),
					);
		}
		$response = new JsonResponse();
		$response->setData($return);
		return $response;
	}


    /**
     * Lists all Article entities.
     *
     */
    public function indexAction()
    {
        return $this->render('TribucaMainBundle:SearchEngine:index.html.twig');
    }
}

