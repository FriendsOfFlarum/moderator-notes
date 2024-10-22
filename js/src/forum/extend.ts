import Extend from 'flarum/common/extenders';
import ModeratorNote from './model/ModeratorNote';
import User from 'flarum/common/models/User';
import ModeratorNotesPage from './components/ModeratorNotesPage';

export default [
  new Extend.Store() //
    .add('moderatorNote', ModeratorNote),

  new Extend.Model(User) //
    .attribute<boolean>('canViewModeratorNotes')
    .attribute<boolean>('canCreateModeratorNotes')
    .attribute<boolean>('canDeleteModeratorNotes')
    .attribute<number>('moderatorNoteCount'),

  new Extend.Routes() //
    .add('user.notes', '/u/:username/notes', ModeratorNotesPage),
];
