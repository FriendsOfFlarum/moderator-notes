import app from 'flarum/forum/app';
import addModeratorNotesPage from './addModeratorNotesPage';

export { default as extend } from './extend';

app.initializers.add('fof-moderator-notes', () => {
  addModeratorNotesPage();
});
