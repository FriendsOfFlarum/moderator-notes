import Component from 'flarum/Component';
import app from 'flarum/app';
import LoadingIndicator from 'flarum/components/LoadingIndicator';
import NoteListItem from './NoteListItem';
import Button from 'flarum/components/Button';
import NoteCreate from './ModeratorNotesCreate';

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

    const { user } = this.props.params;

    return (
      <div className="DiscussionList">
        <h1 className="DiscussionList-notes">{app.translator.trans('fof-moderator-notes.forum.user.notes')}</h1>
        {user.canCreateModeratorNotes() ? (
        <div>
          <Button 
            className="Button Button--primary Button--block" 
            onclick={this.handleOnClickCreate.bind(this)}>
            {app.translator.trans('fof-moderator-notes.forum.moderatorNotes.add_button')}
          </Button>
        </div>) : (<div/>)}
        <p></p>
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
