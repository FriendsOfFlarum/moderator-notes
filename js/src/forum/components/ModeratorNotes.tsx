import Component, { ComponentAttrs } from 'flarum/common/Component';
import app from 'flarum/forum/app';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import NoteListItem from './NoteListItem';
import Button from 'flarum/common/components/Button';
import NoteCreate from './ModeratorNotesCreate';
import listItems from 'flarum/common/helpers/listItems';
import ItemList from 'flarum/common/utils/ItemList';
import User from 'flarum/common/models/User';
import Mithril from 'mithril';
import ModeratorNote from '../model/ModeratorNote';

export interface ModeratorNotesAttrs extends ComponentAttrs {
  user: User;
  sort: string;
}

export default class ModeratorNotes extends Component<ModeratorNotesAttrs> {
  loading: boolean = false;
  notes: ModeratorNote[] = [];
  user!: User;
  loadLimit: number = 20;

  oninit(vnode: Mithril.Vnode<ModeratorNotesAttrs>) {
    super.oninit(vnode);

    this.user = this.attrs.user;

    this.loadResults();
  }

  view() {
    return (
      <div className="ModeratorNotesList">
        <h1 className="ModeratorNotesList-notes">{app.translator.trans('fof-moderator-notes.forum.user.notes')}</h1>
        <div class="ModeratorNotes-toolbar">
          <ul className="ModeratorNotes-toolbar-action">{listItems(this.actionItems().toArray())}</ul>
        </div>
        {this.loading ? (
          <LoadingIndicator display="block" />
        ) : (
          <ul className="ModeratorNotesList-discussions">
            {this.notes.map((note) => {
              return (
                <li key={note.id()} data-id={note.id()}>
                  {NoteListItem.component({ note })}
                </li>
              );
            })}
            {!this.loading && this.notes.length === 0 && <label>{app.translator.trans('fof-moderator-notes.forum.moderatorNotes.noNotes')}</label>}
          </ul>
        )}
      </div>
    );
  }

  actionItems(): ItemList<Mithril.Children> {
    const items = new ItemList<Mithril.Children>();
    const canCreateNote = app.session.user?.canCreateModeratorNotes();

    canCreateNote &&
      items.add(
        'create_note',
        <Button className="Button Button--primary" onclick={this.handleOnClickCreate.bind(this)}>
          {app.translator.trans('fof-moderator-notes.forum.moderatorNotes.add_button')}
        </Button>
      );

    return items;
  }

  async loadResults(offset = 0) {
    this.loading = true;

    const results = await app.store.find<ModeratorNote[]>('moderatorNote', {
      filter: {
        subject: this.user.id() as string,
      },
      page: { offset, limit: this.loadLimit },
      sort: '-createdAt',
    });

    this.notes = results;

    this.loading = false;

    m.redraw();
  }

  handleOnClickCreate(e: Event) {
    e.preventDefault();
    app.modal.show(NoteCreate, {
      callback: this.loadResults.bind(this),
      ...this.attrs,
    });
  }
}
