import app from 'flarum/forum/app';
import addModeratorNotesPage from './addModeratorNotesPage';
import ModeratorNote from './model/ModeratorNote';
import User from 'flarum/common/models/User';
import Model from 'flarum/common/Model';

app.initializers.add('fof-moderator-notes', () => {
  app.store.models.moderatorNote = ModeratorNote;
  User.prototype.canViewModeratorNotes = Model.attribute('canViewModeratorNotes');
  User.prototype.canCreateModeratorNotes = Model.attribute('canCreateModeratorNotes');
  User.prototype.canDeleteModeratorNotes = Model.attribute('canDeleteModeratorNotes');
  addModeratorNotesPage();
});
