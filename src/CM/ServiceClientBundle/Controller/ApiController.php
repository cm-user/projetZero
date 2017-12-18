<?php
namespace CM\ServiceClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CM\ServiceClientBundle\Entity\Mail;
use CM\ServiceClientBundle\Entity\Branch;
use CM\ServiceClientBundle\Entity\Solution;
use FOS\RestBundle\Controller\Annotations\Get;


class ApiController extends Controller
{

    public function getBranchesAction() //liste toutes les branches
    {
        $branches = $this->get('doctrine.orm.entity_manager')
            ->getRepository('ServiceClientBundle:Branch')
            ->findAll();
        /* @var $branches Branch[] */

        $formatted = [];
        foreach ($branches as $branch) {
            $formatted[] = [
                'id' => $branch->getId(),
                'nom' => $branch->getNom(),
            ];
        }

        return new JsonResponse($formatted);
    }

    /**
     * @Get("/solution_mail/{id}")
     */
    public function getSolutionMailAction($id, Request $request) //Liste toutes les solutions et mails associés d'une branche
    {
        $query = $this->getDoctrine()->getManager()
            ->createQuery('SELECT  s, m FROM CM\ServiceClientBundle\Entity\Solution s LEFT JOIN s.mails m LEFT JOIN s.branche b where s.branche = :id ' );
        $query->setParameter('id', $id);
        $result=$query->getArrayResult();

        $array_solution = [];
        $i = 0;
        foreach ($result as $solution) {
            $array_solution[] = [
                'id' => $result[$i]['id'],
                'nom' => $result[$i]['nom'],
                'text' => $result[$i]['textSolution'],
                'mails' => $result[$i]['mails'],
            ];
            $i++;
        }
        return new JsonResponse($array_solution);
    }

    /**
     * @Get("/branche/{id}")
     */
    public function getBranchAction($id, Request $request)  //Liste tous les enfants d'une branche
    {

        $query = $this->getDoctrine()->getManager()
            ->createQuery('SELECT  b, c FROM CM\ServiceClientBundle\Entity\Branch b LEFT JOIN b.children c where b.id = :id ');
        $query->setParameter('id', $id);
        $result = $query->getArrayResult();


        return new JsonResponse($result[0]['children']);

    }

    /**
     * @Get("/branche_parent")
     */
    public function getBranchParentAction( Request $request) //Liste toutes les branches n'ayant aucun parent
    {
        //////////DONNE TOUTES LES BRANCHES N'AYANT PAS DE PARENT/////////
        $query = $this->getDoctrine()->getManager()
            ->createQuery('SELECT b, c FROM CM\ServiceClientBundle\Entity\Branch b LEFT JOIN b.children c WHERE b.parent IS NULL ' );
        $children=$query->getResult();

        $array_solution = [];
        foreach ($children as $child) {
            $array_solution[] = [
                'id' => $child->getId(),
                'nom' => $child->getNom(),

            ];
        }
        return new JsonResponse($array_solution);
    }


    /**
     * @Get("/recherche/{name}")
     */
    public function getRechercheAction($name, Request $request) //Liste tous les mails d'une branche
    {
        $query = $this->getDoctrine()->getManager()
            ->createQuery("SELECT b FROM CM\ServiceClientBundle\Entity\Branch b WHERE b.nom LIKE :name ");
        $query->setParameter('name', '%'.$name.'%');
        $branches=$query->getResult();

        $array_solution = [];
        foreach ($branches as $branche) {
            $array_solution[] = [
                'id' => $branche->getId(),
                'nom' => $branche->getNom(),

            ];
        }
        return new JsonResponse($array_solution);
    }

    /**
     * @Get("/branche_nom/{name}")
     */
    public function getBrancheByNomAction($name, Request $request) //Liste tous les mails d'une branche
    {
        $query = $this->getDoctrine()->getManager()
            ->createQuery("SELECT b FROM CM\ServiceClientBundle\Entity\Branch b WHERE b.nom LIKE :name ");
        $query->setParameter('name', $name);
        $branches=$query->getResult();

        $array_solution = [];
        foreach ($branches as $branche) {
            $array_solution[] = [
                'id' => $branche->getId(),
                'nom' => $branche->getNom(),

            ];
        }
        return new JsonResponse($array_solution);
    }
}