import Component from 'flarum/Component';
import username from 'flarum/helpers/username';
import fullTime from 'flarum/helpers/fullTime';
import extractText from 'flarum/utils/extractText';
import avatar from 'flarum/helpers/avatar';

export default class NoteListItem extends Component {
    view() {
        const { note } = this.props;
        const addedByUser = note.addedByUser();
        const formatedDate = fullTime(note.createdAt());

        return (
            <div className="ModeratorNotesListItem">
                <div className="ModeratorNotesListItem-main">
                    <div className="ModeratorNotesListItem-title">
                        <a
                            href={addedByUser ? app.route.user(addedByUser) : '#'}
                            className="ModeratorNotesListItem-author"
                            title={extractText(
                                app.translator.trans('fof-moderator-notes.forum.moderatorNotes.created_text', {
                                    user: addedByUser,
                                    date: formatedDate,
                                })
                            )}
                            config={function(element) {
                                $(element).tooltip({ placement: 'right' });
                                m.route.apply(this, arguments);
                            }}
                        >
                            {avatar(addedByUser, { title: '' })} {username(addedByUser)}
                        </a>
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
}
