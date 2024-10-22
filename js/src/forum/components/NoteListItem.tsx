import app from 'flarum/forum/app';
import Component, { ComponentAttrs } from 'flarum/common/Component';
import username from 'flarum/common/helpers/username';
import fullTime from 'flarum/common/helpers/fullTime';
import extractText from 'flarum/common/utils/extractText';
import avatar from 'flarum/common/helpers/avatar';
import Dropdown from 'flarum/common/components/Dropdown';
import ItemList from 'flarum/common/utils/ItemList';
import Button from 'flarum/common/components/Button';
import Link from 'flarum/common/components/Link';
import Tooltip from 'flarum/common/components/Tooltip';
import ModeratorNote from '../model/ModeratorNote';
import type Mithril from 'mithril';

export interface NoteListItemAttrs extends ComponentAttrs {
  note: ModeratorNote;
}

export default class NoteListItem extends Component<NoteListItemAttrs> {
  view() {
    const { note } = this.attrs;
    const addedByUser = note.addedByUser();
    const createdAt = note.createdAt();
    const formatedDate = createdAt instanceof Date ? fullTime(createdAt) : '';
    const actions = this.noteActions(note).toArray();

    return (
      <div className="ModeratorNotesListItem">
        {actions.length &&
          Dropdown.component(
            {
              icon: 'fas fa-ellipsis-v',
              className: 'ModeratorNotesListItem-controls',
              buttonClassName: 'Button Button--icon Button--flat Slidable-underneath Slidable-underneath--right',
            },
            actions
          )}
        <div className="ModeratorNotesListItem-main">
          <div className="ModeratorNotesListItem-title">
            <Tooltip
              text={extractText(
                app.translator.trans('fof-moderator-notes.forum.moderatorNotes.created_text', {
                  username: addedByUser ? addedByUser.displayName() : '',
                  date: formatedDate,
                })
              )}
              position="right"
            >
              <Link href={addedByUser ? app.route.user(addedByUser) : '#'} className="ModeratorNotesListItem-author">
                {addedByUser && avatar(addedByUser)} {addedByUser && username(addedByUser)}
              </Link>
            </Tooltip>
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

  noteActions(context: ModeratorNote): ItemList<Mithril.Children> {
    const actions = new ItemList<Mithril.Children>();

    if (app.session.user?.canDeleteModeratorNotes()) {
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

    return actions;
  }

  async deleteNote(note: ModeratorNote) {
    if (confirm(extractText(app.translator.trans('fof-moderator-notes.forum.moderatorNotes.confirm'))) === true) {
      try {
        await note.delete();
      } catch {}
      location.reload();
    }
    return Promise.resolve();
  }
}
