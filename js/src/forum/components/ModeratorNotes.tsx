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
import Dropdown from 'flarum/common/components/Dropdown';
import Placeholder from 'flarum/common/components/Placeholder';
import { ApiResponsePlural } from 'flarum/common/Store';
import Switch from 'flarum/common/components/Switch';

export interface ModeratorNotesAttrs extends ComponentAttrs {
  user: User;
  sort: string;
}

export default class ModeratorNotes extends Component<ModeratorNotesAttrs> {
  loading: boolean = false;
  notes: ModeratorNote[] = [];
  user!: User;
  loadLimit: number = 20;
  sort: string = 'newest';
  areMoreResults: boolean = false;
  filterByAuthor: boolean = false;

  oninit(vnode: Mithril.Vnode<ModeratorNotesAttrs>) {
    super.oninit(vnode);

    this.user = this.attrs.user;

    this.loadResults();
  }

  view() {
    let loading;
    if (this.loading) {
      loading = <LoadingIndicator display="block" />;
    } else if (this.areMoreResults) {
      loading = (
        <Button
          className="Button"
          onclick={() => {
            this.loadResults(this.notes.length);
          }}
        >
          {app.translator.trans('core.forum.discussion_list.load_more_button')}
        </Button>
      );
    }

    let content;
    if (!this.loading && this.notes.length === 0) {
      content = <Placeholder text={app.translator.trans('fof-moderator-notes.forum.moderatorNotes.noNotes')} />;
    } else {
      content = (
        <ul className="ModeratorNotesList-discussions">
          {this.notes.map((note) => {
            return (
              <li key={note.id()} data-id={note.id()}>
                <NoteListItem note={note} onDelete={() => this.handleDelete(note)} />
              </li>
            );
          })}
        </ul>
      );
    }

    return (
      <div className="ModeratorNotesList">
        <h1 className="ModeratorNotesList-notes">{app.translator.trans('fof-moderator-notes.forum.user.notes')}</h1>
        <div class="ModeratorNotes-toolbar">
          <ul className="ModeratorNotes-toolbar-action">{listItems(this.actionItems().toArray())}</ul>
        </div>
        <div className="ModeratorNotesList-notes">{content}</div>
        <div className="ModeratorNotesList-loadMore">{loading}</div>
      </div>
    );
  }

  sortmap() {
    const map: any = {};

    map.newest = '-createdAt';
    map.oldest = 'createdAt';

    return map;
  }

  actionItems(): ItemList<Mithril.Children> {
    const items = new ItemList<Mithril.Children>();
    const canCreateNote = app.session.user?.canCreateModeratorNotes();

    const sortOptions = Object.keys(this.sortmap()).reduce((acc: any, sortId) => {
      acc[sortId] = app.translator.trans(`core.forum.index_sort.${sortId}_button`);
      return acc;
    }, {});

    canCreateNote &&
      items.add(
        'create_note',
        <Button className="Button Button--primary Button--new-note" icon="fas fa-edit" onclick={this.handleOnClickCreate.bind(this)}>
          {app.translator.trans('fof-moderator-notes.forum.moderatorNotes.add_button')}
        </Button>,
        100
      );

    items.add(
      'sort',
      <Dropdown
        buttonClassName="Button"
        label={sortOptions[this.sort] || sortOptions[Object.keys(this.sortmap())[0]]}
        accessibleToggleLabel={app.translator.trans('core.forum.index_sort.toggle_dropdown_accessible_label')}
      >
        {Object.keys(sortOptions).map((value) => {
          const label = sortOptions[value];
          const active = this.sort === value;

          return (
            <Button
              icon={active ? 'fas fa-check' : true}
              onclick={() => {
                this.setSort(value);
              }}
              active={active}
            >
              {label}
            </Button>
          );
        })}
      </Dropdown>,
      80
    );

    items.add(
      'filterByAuthor',
      <Switch
        state={this.filterByAuthor}
        onchange={(value: boolean) => {
          this.filterByAuthor = value;
          this.loadResults();
        }}
      >
        {app.translator.trans('fof-moderator-notes.forum.moderatorNotes.filter_by_me')}
      </Switch>,
      60
    );

    return items;
  }

  setSort(sort: string) {
    this.sort = sort;
    this.loadResults();
  }

  async loadResults(offset = 0) {
    this.loading = true;

    const filters: any = {
      subject: this.user.id() as string,
    };

    if (this.filterByAuthor && app.session.user) {
      filters.author = app.session.user.id();
    }

    const results = await app.store.find<ModeratorNote[]>('moderatorNote', {
      filter: filters,
      page: { offset, limit: this.loadLimit },
      sort: this.sortmap()[this.sort],
    });

    this.notes = offset === 0 ? results : this.notes.concat(results);

    this.loading = false;
    this.areMoreResults = this.moreResults(results);
    m.redraw();
  }

  handleOnClickCreate(e: Event) {
    e.preventDefault();
    app.modal.show(NoteCreate, {
      callback: this.loadResults.bind(this),
      ...this.attrs,
    });
  }

  moreResults(results: ApiResponsePlural<ModeratorNote>): boolean {
    // Either this will be undefined (no more results) or the URL to the next page
    return !!results.payload.links?.next;
  }

  async handleDelete(note: ModeratorNote) {
    // Remove the note from the list
    this.notes = this.notes.filter((n) => n.id() !== note.id());
    m.redraw();
  }
}
