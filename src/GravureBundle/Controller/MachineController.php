<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 15/03/2018
 * Time: 11:51
 */

namespace GravureBundle\Controller;


use GravureBundle\Entity\Domain\Machine;
use GravureBundle\Form\MachineSubmission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Machine controller.
 *
 * @Route("machine")
 */
class MachineController extends Controller
{


    /**
     * @Route("/", name="machine_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        $machines = $this->get('repositories.machine')->findAll();

        return $this->render('GravureBundle:machine:index.html.twig', array(
            'machines' => $machines,
        ));
    }

    /**
     * @Route("/new", name="machine_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $machineSubmission = new MachineSubmission();
        $form = $this->createForm('GravureBundle\Form\Types\MachineType',$machineSubmission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $machine = Machine::addMachine($machineSubmission);
            $this->get('repositories.machine')->save($machine);

            return $this->redirectToRoute('machine_index');
        }

        return $this->render('GravureBundle:machine:new.html.twig', array(
            'machine' => $machineSubmission,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/edit", name="machine_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
        $machine = $this->get('repositories.machine')->findById($id);
        $machine->setId($id);

        $deleteForm = $this->createDeleteForm($machine);
        $editForm = $this->createForm('GravureBundle\Form\Types\MachineType', $machine);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->get('repositories.machine')->update($machine);

            return $this->redirectToRoute('machine_index');
        }

        return $this->render('GravureBundle:machine:edit.html.twig', array(
            'machine' => $machine,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="machine_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $machine = $this->get('repositories.machine')->findById($id);
        $machine->setId($id);

        $form = $this->createDeleteForm($machine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('repositories.machine')->delete($id);
        }

        return $this->redirectToRoute('machine_index');
    }

    private function createDeleteForm(Machine $machine)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('machine_delete', array('id' => $machine->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

//    /**
//     * @Route("/machine/json", name="machine_json")
//     */
//    public function getMachineJsonAction()
//    {
//        $machines = $this->get('repositories.machine')->findAllWithoutNull();
//
//        return new JsonResponse($machines);
//    }

    /**
     * @Route("/machine-use/session/{id}", name="machine_use_session", options={"expose"=true})
     */
    public function getMachineJsonAction($id)
    {
        $this->get('session')->set('id_machine_used', $id); //on stock l'id machine
        $machine = $this->get('repositories.machine')->findById($id);

        return new JsonResponse($machine->getColor());
    }

    /**
     * @Route("/default/{id}", name="machine_default_change", options={"expose"=true})
     */
    public function updateMachineDefault($id)
    {
        $this->get('repositories.machine')->updateDefault($id);

        return new JsonResponse("machine default change");
    }

    /**
     * @Route("/default-machine/color", name="machine_default_color", options={"expose"=true})
     */
    public function getDefaultMachineColor()
    {
       $color = $this->get('repositories.machine')->getDefaultColor();

        return new JsonResponse($color);
    }

}