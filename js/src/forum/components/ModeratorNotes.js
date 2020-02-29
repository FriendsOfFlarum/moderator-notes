import Component from 'flarum/Component';
import app from 'flarum/app';
import LoadingIndicator from 'flarum/components/LoadingIndicator';
import NoteListItem from './NoteListItem';
import Button from 'flarum/components/Button';
import NoteCreate from './ModeratorNotesCreate';
import listItems from 'flarum/helpers/listItems';
import ItemList from 'flarum/utils/ItemList';

export default class ModeratorNotes extends Component {
    init() {
        super.init();
        this.loading = true;
        this.notes = [];
        this.refresh();
    }

    view() {

        let loading;

        if (this.loading) {
            loading = LoadingIndicator.component();
        }



        return (
            <div className="DiscussionList">
                <h1 className="DiscussionList-notes">{app.translator.trans('fof-moderator-notes.forum.user.notes')}</h1>
                <div class="ModeratorNotes-toolbar">
                    <ul className="ModeratorNotes-toolbar-action">{listItems(this.actionItems().toArray())}</ul>
                </div>
                <ul className="DiscussionList-discussions">
                    {this.notes.map(note => {
                        return (
                            <li key={note.id()} data-id={note.id()}>
                                {NoteListItem.component({ note })}
                            </li>
                        );
                    })}
                    {!this.loading
                        && this.notes.length === 0
                        && <label>{app.translator.trans('fof-moderator-notes.forum.moderatorNotes.noNotes')}</label>}
                </ul>
                <div className="DiscussionList-loadMore">
                    {loading}
                </div>
            </div>
        );
    }

    actionItems() {
        const { user } = this.props.params;
        const items = new ItemList();
        const canCreateNote = user.canCreateModeratorNotes();

        items.add('create_note',
            Button.component({
                children: app.translator.trans('fof-moderator-notes.forum.moderatorNotes.add_button'),
                className: "Button Button--primary",
                onclick: this.handleOnClickCreate.bind(this),
                disabled: !canCreateNote
            })
        );

        return items;
    }

    parseResults(results) {
        [].push.apply(this.notes, results);
        this.loading = false;
        m.lazyRedraw();

        return results;
    }

    refresh() {
        return app.store.find('notes', this.props.params.user.id()).then(
            results => {
                this.notes = [];
                this.parseResults(results);
            },
            () => {
                this.loading = false;
                m.redraw();
            }
        )
    }

    handleOnClickCreate(e) {
        e.preventDefault();
        app.modal.show(new NoteCreate(this.props.params));
    }
}
