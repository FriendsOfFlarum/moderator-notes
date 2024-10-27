import app from 'flarum/forum/app';
import addModeratorNotesPage from './addModeratorNotesPage';

export { default as extend } from './extend';

export * from './components';
export * from './model';

app.initializers.add('fof-moderator-notes', () => {
  addModeratorNotesPage();
});
