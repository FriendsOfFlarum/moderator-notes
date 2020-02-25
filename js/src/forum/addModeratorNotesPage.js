import {extend} from 'flarum/extend';
import UserPage from 'flarum/components/UserPage';
import LinkButton from 'flarum/components/LinkButton';
import ModeratorNotesPage from "./components/ModeratorNotesPage";
import Model from 'flarum/Model';
import User from 'flarum/models/User';

export default function () {
  User.prototype.canViewModeratorNotes = Model.attribute('canViewModeratorNotes');

  app.routes['user.notes'] = {path: '/u/:username/notes', component: ModeratorNotesPage.component()};

  extend(UserPage.prototype, 'navItems', function (items) {
    if (this.user.canViewModeratorNotes()) {
      items.add('notes',
        LinkButton.component({
          href: app.route('user.notes', {username: this.user.username()}),
          children: app.translator.trans('fof-moderator-notes.forum.user.notes'),
          icon: 'fas fa-sticky-note'
        }),
        10
      );
    }
  })
}
