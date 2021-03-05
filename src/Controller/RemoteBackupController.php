<?php

namespace Herisson\Controller;

use Herisson\Entity\RemoteBackup;
use Herisson\Form\RemoteBackupType;
use Herisson\Repository\RemoteBackupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/remote/backup")
 */
class RemoteBackupController extends AbstractController
{
    /**
     * @Route("/", name="remote_backup_index", methods={"GET"})
     */
    public function index(RemoteBackupRepository $remoteBackupRepository): Response
    {
        return $this->render('remote_backup/index.html.twig', [
            'remote_backups' => $remoteBackupRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="remote_backup_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $remoteBackup = new RemoteBackup();
        $form = $this->createForm(RemoteBackupType::class, $remoteBackup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($remoteBackup);
            $entityManager->flush();

            return $this->redirectToRoute('remote_backup_index');
        }

        return $this->render('remote_backup/new.html.twig', [
            'remote_backup' => $remoteBackup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="remote_backup_show", methods={"GET"})
     */
    public function show(RemoteBackup $remoteBackup): Response
    {
        return $this->render('remote_backup/show.html.twig', [
            'remote_backup' => $remoteBackup,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="remote_backup_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RemoteBackup $remoteBackup): Response
    {
        $form = $this->createForm(RemoteBackupType::class, $remoteBackup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('remote_backup_index');
        }

        return $this->render('remote_backup/edit.html.twig', [
            'remote_backup' => $remoteBackup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="remote_backup_delete", methods={"DELETE"})
     */
    public function delete(Request $request, RemoteBackup $remoteBackup): Response
    {
        if ($this->isCsrfTokenValid('delete'.$remoteBackup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($remoteBackup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('remote_backup_index');
    }
}
