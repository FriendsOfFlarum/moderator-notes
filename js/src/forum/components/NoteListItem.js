import Component from 'flarum/common/Component';
import username from 'flarum/common/helpers/username';
import fullTime from 'flarum/common/helpers/fullTime';
import extractText from 'flarum/common/utils/extractText';
import avatar from 'flarum/common/helpers/avatar';
import Dropdown from 'flarum/common/components/Dropdown';
import ItemList from 'flarum/common/utils/ItemList';
import Button from 'flarum/common/components/Button';
import Link from 'flarum/common/components/Link';

export default class NoteListItem extends Component {
    view() {
        const { note } = this.attrs;
        const addedByUser = note.addedByUser();
        const formatedDate = fullTime(note.createdAt());
        const actions = this.noteActions(note);

        return (
            <div className="ModeratorNotesListItem">
                {actions.length
                    ? Dropdown.component(
                          {
                              icon: 'fas fa-ellipsis-v',
                              className: 'ModeratorNotesListItem-controls',
                              buttonClassName: 'Button Button--icon Button--flat Slidable-underneath Slidable-underneath--right',
                          },
                          actions
                      )
                    : ''}
                <div className="ModeratorNotesListItem-main">
                    <div className="ModeratorNotesListItem-title">
                        <Link
                            href={addedByUser ? app.route.user(addedByUser) : '#'}
                            className="ModeratorNotesListItem-author"
                            title={extractText(
                                app.translator.trans('fof-moderator-notes.forum.moderatorNotes.created_text', {
                                    user: addedByUser,
                                    date: formatedDate,
                                })
                            )}
                            config={function (element) {
                                $(element).tooltip({ placement: 'right' });
                                m.route.apply(this, arguments);
                            }}
                        >
                            {avatar(addedByUser, { title: '' })} {username(addedByUser)}
                        </Link>
                    </div>
                    <p>{formatedDate}</p>
                    <hr />
                    <div className="ModeratorNotesListItem-note">
                        <ul className="ModeratorNotesListItem-info">
                            <li className="item-content">
                                <span>{m.trust(note.note())}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        );
    }

    noteActions(context) {
        const actions = new ItemList();

        if (app.session.user.canDeleteModeratorNotes()) {
            actions.add(
                'delete',
                Button.component(
                    {
                        icon: 'far fa-trash-alt',
                        onclick: () => this.deleteNote(context),
                    },
                    app.translator.trans('fof-moderator-notes.forum.moderatorNotes.delete')
                )
            );
        }

        return actions.toArray();
    }

    deleteNote(note) {
        if (confirm(app.translator.trans('fof-moderator-notes.forum.moderatorNotes.confirm')) === true) {
            return note
                .delete()
                .then(() => {})
                .catch(() => {})
                .then(() => {
                    location.reload();
                });
        }
    }
}
