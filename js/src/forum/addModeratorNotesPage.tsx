import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import UserPage from 'flarum/forum/components/UserPage';
import LinkButton from 'flarum/common/components/LinkButton';

export default function addModeratorNotesPage() {
  extend(UserPage.prototype, 'navItems', function (items) {
    if (app.session.user?.canViewModeratorNotes() && this.user) {
      items.add(
        'notes',
        <LinkButton icon="fas fa-sticky-note" href={app.route('user.notes', { username: this.user.slug() })}>
          {app.translator.trans('fof-moderator-notes.forum.user.notes')}
          {this.user.moderatorNoteCount() > 0 && <span className="Button-badge">{this.user.moderatorNoteCount()}</span>}
        </LinkButton>,
        10
      );
    }
  });
}
