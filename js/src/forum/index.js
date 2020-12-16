import app from 'flarum/app';
import addModeratorNotesPage from './addModeratorNotesPage';
import ModeratorNote from './model/ModeratorNote';
import { Extend } from '@flarum/core/forum';
import User from 'flarum/models/User';
import Model from 'flarum/Model';

app.initializers.add('fof-moderator-notes', (app) => {
    app.store.models.notes = ModeratorNote;
    User.prototype.canViewModeratorNotes = Model.attribute('canViewModeratorNotes');
    User.prototype.canCreateModeratorNotes = Model.attribute('canCreateModeratorNotes');
    User.prototype.canDeleteModeratorNotes = Model.attribute('canDeleteModeratorNotes');
    addModeratorNotesPage();
});

export const extend = [new Extend.Model('moderatorNotes', ModeratorNote)];
