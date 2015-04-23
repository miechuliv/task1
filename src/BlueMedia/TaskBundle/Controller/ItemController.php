<?php

namespace BlueMedia\TaskBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use BlueMedia\TaskBundle\Entity\Item;
use BlueMedia\TaskBundle\Form\ItemType;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\QueryBuilder;

/**
 * Item controller.
 *
 */
class ItemController extends FOSRestController {

    /**
     * to jest spoko
     * @return json
     */
    public function getItemsAction(Request $request) {
        $instock = $request->query->get('inStock');
        $minStock = $request->query->get('minStock');
        
        if ($instock == 'true') {
            $items = $this->getDoctrine()->getRepository("BlueMediaTaskBundle:Item")->createQueryBuilder('i')
                            ->where('i.amount > :amount')
                            ->setParameter('amount', '0')
                            ->getQuery()->getResult();
        } else if ($instock == 'false') {
            $items = $this->getDoctrine()->getRepository("BlueMediaTaskBundle:Item")->findBy(array(
                'amount' => '0'
            ));
        } else if (isset($minStock) && $minStock != NULL) {
            $items = $this->getDoctrine()->getRepository("BlueMediaTaskBundle:Item")->createQueryBuilder('i')
                            ->where('i.amount > :amount')
                            ->setParameter('amount', $minStock)
                            ->getQuery()->getResult();
        } else {
            $items = $this->getDoctrine()->getRepository("BlueMediaTaskBundle:Item")->findAll();
        }
        
        $view = $this->view($items, 200)
                ->setFormat('json');

        return $this->handleView($view);
    }

    /**
     * Jesli nie ma item o tym id to zwroc 404
     * @return json
     */
    public function getItemAction($id, Request $request) {
        $item = $this->getDoctrine()->getRepository("BlueMediaTaskBundle:Item")->find($id);
        
        if(!$item)
        {
            $view = $this->view(array(
                'error_msg' => 'no item found',
                'result' => 'failure'
            ), 404)
                ->setFormat('json');
            
            return $this->handleView($view);
        }
        
        $view = $this->view($item, 200)
                ->setFormat('json');

        return $this->handleView($view);
    }

    /**
     * to przecież nie potrzebne na serwerowej
     *
     */
   /* public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BlueMediaTaskBundle:Item')->findAll();

        return $this->render('BlueMediaTaskBundle:Item:index.html.twig', array(
                    'entities' => $entities,
        ));
    }*/

    /**
     * dodanie itemu
     */
    public function createItemAction(Request $request) {
        
        $name = $request->query->get('name');
        $stock = $request->query->get('stock');
        
        // jak nie ma podanego name  to nie mozna dodac itemu
        // mozesz tu dodac jakies symfony walidacjie typu tam czy name to string i 
        // czy stock to na pewno liczba itp
        if(!$name)
        {
            $view = $this->view(array(
                'error_msg' => 'name required',
                'result' => 'failure'
            ), 404)
                ->setFormat('json');
            
            return $this->handleView($view);
        }
        
        $item = new Item();
        // jakas filtracja na ten name np: trim zeby nie bylo ze tak
        // gole parametry od usera przyjmujesz 
        $item->setName(trim($name));
        $item->setAmount((int)$stock);
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($item);
        $em->flush();
        
        // nie wiem czy zwraca caly produkt czy tylko info o sukcesie
        $view = $this->view(array(
            'result' => 'sucess',
            'new_item_id' => $item->getId(),
            
        ), 200)
                ->setFormat('json');
        
        return $this->handleView($view);
    }

    /**
     * to nie potrzebne bo nie ma formularzy na serwerowej
     * 
     * @param Item $entity
     * @return type
     */
   /* private function createCreateForm(Item $entity) {
        $form = $this->createForm(new ItemType(), $entity, array(
            'action' => $this->generateUrl('item_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * to nie potrzebne bo to tylko renderuje formularz
     *
     */
    /*public function newAction() {
        $entity = new Item();
        $form = $this->createCreateForm($entity);

        return $this->render('BlueMediaTaskBundle:Item:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }*/

    /**
     * to nie potrzebne
     *
     */
    /*public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlueMediaTaskBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BlueMediaTaskBundle:Item:show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),
        ));
    }*/

    /**
     * do kibla
     *
     */
    /*public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlueMediaTaskBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BlueMediaTaskBundle:Item:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }*/

    /**
     * do kibla
     */
    /*private function createEditForm(Item $entity) {
        $form = $this->createForm(new ItemType(), $entity, array(
            'action' => $this->generateUrl('item_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }*/

    /**
     * Eupdate itemu
     *
     */
    public function updateItemAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $item = $em->getRepository('BlueMediaTaskBundle:Item')->find($id);

        $name = $request->query->get('name');
        $stock = $request->query->get('stock');
        
        if(!$item)
        {
            $view = $this->view(array(
                'error_msg' => 'no item found',
                'result' => 'failure'
            ), 404)
                ->setFormat('json');
            
            return $this->handleView($view);
        }
        
       
        // jakas filtracja na ten name np: trim zeby nie bylo ze tak
        // gole parametry od usera przyjmujesz 
        if($name)
        {
           $item->setName(trim($name)); 
        }
        if($stock)
        {
            $item->setAmount((int)$stock);
        }
        
        
        $em->persist($item);
        $em->flush();
        
        // nie wiem czy zwraca caly produkt czy tylko info o sukcesie
        $view = $this->view(array(
            'result' => 'sucess'
        ), 200)
                ->setFormat('json');
        
        return $this->handleView($view);
    }

    /**
     * usuniecie itemu
     *
     */
    public function deleteItemAction(Request $request, $id) {
        

       
            $em = $this->getDoctrine()->getManager();
            $item = $em->getRepository('BlueMediaTaskBundle:Item')->find($id);

            if(!$item)
                {
                    $view = $this->view(array(
                        'error_msg' => 'no item found',
                        'result' => 'failure'
                    ), 404)
                        ->setFormat('json');
                    
                    return $this->handleView($view);
                }


            $em->remove($item);
            $em->flush();
        

        $view = $this->view(array(
            'result' => 'sucess'
        ), 200)
                ->setFormat('json');
        
        return $this->handleView($view);
    }

    /**
     * do kibla
     */
    /*private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('item_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }*/

}
