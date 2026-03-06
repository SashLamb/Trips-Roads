<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Comments Controller
 *
 * @property \App\Model\Table\CommentsTable $Comments
 */
class CommentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {

        $query = $this->Comments->find()
            ->contain(['Users', 'Roadtrips', 'PointsOfInterests']);
        $comments = $this->paginate($query);

        $this->set(compact('comments'));
    }

    /**
     * View method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $comment = $this->Comments->get($id, contain: ['Users', 'Roadtrips', 'PointsOfInterests']);
        $this->set(compact('comment'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    // src/Controller/CommentsController.php

    public function add()
    {
        $comment = $this->Comments->newEmptyEntity();
        if ($this->request->is('post')) {
            $comment = $this->Comments->patchEntity($comment, $this->request->getData());
            // On force l'ID de l'utilisateur connecté
            $comment->user_id = $this->request->getAttribute('identity')->getIdentifier();

            if ($this->Comments->save($comment)) {
                $this->Flash->success(__('Votre avis a été publié.'));
                return $this->redirect($this->referer());
            }
            $this->Flash->error(__('Erreur lors de la sauvegarde.'));
        }
        return $this->redirect($this->referer());
    }

    /**
     * Edit method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $comment = $this->Comments->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $comment = $this->Comments->patchEntity($comment, $this->request->getData());

            if ($this->Comments->save($comment)) {
                $this->Flash->success(__('The comment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The comment could not be saved. Please, try again.'));
        }
        $users = $this->Comments->Users->find('list', limit: 200)->all();
        $roadtrips = $this->Comments->Roadtrips->find('list', limit: 200)->all();
        $pointsOfInterests = $this->Comments->PointsOfInterests->find('list', limit: 200)->all();
        $this->set(compact('comment', 'users', 'roadtrips', 'pointsOfInterests'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $comment = $this->Comments->get($id);

        $currentUser = $this->request->getAttribute('identity');
        $userId = $currentUser->getIdentifier();
        $userRole = $currentUser->role ?? 'user';

        if ($comment->user_id !== $userId && $userRole !== 'admin') {
            $this->Flash->error(__('Vous n\'avez pas les droits pour supprimer ce commentaire.'));
            return $this->redirect($this->referer(['action' => 'index']));
        }

        if ($this->Comments->delete($comment)) {
            $this->Flash->success(__('Le commentaire a été supprimé.'));
        } else {
            $this->Flash->error(__('Le commentaire n\'a pas pu être supprimé. Veuillez réessayer.'));
        }

        return $this->redirect($this->referer(['action' => 'index']));
    }
}
