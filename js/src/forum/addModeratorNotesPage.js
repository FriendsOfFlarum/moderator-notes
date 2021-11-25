import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import UserPage from 'flarum/forum/components/UserPage';
import LinkButton from 'flarum/common/components/LinkButton';
import ModeratorNotesPage from './components/ModeratorNotesPage';
import Model from 'flarum/common/Model';
import User from 'flarum/common/models/User';

export default function () {
    User.prototype.canViewModeratorNotes = Model.attribute('canViewModeratorNotes');
    User.prototype.moderatorNoteCount = Model.attribute('moderatorNoteCount');

    app.routes['user.notes'] = { path: '/u/:username/notes', component: ModeratorNotesPage };

    extend(UserPage.prototype, 'navItems', function (items) {
        if (app.session.user && app.session.user.canViewModeratorNotes()) {
            items.add(
                'notes',
                LinkButton.component(
                    {
                        href: app.route('user.notes', { username: this.user.username() }),
                        icon: 'fas fa-sticky-note',
                    },
                    [
                        app.translator.trans('fof-moderator-notes.forum.user.notes'),
                        this.user.moderatorNoteCount() > 0 ? <span className="Button-badge">{this.user.moderatorNoteCount()}</span> : '',
                    ]
                ),
                10
            );
        }
    });
}
