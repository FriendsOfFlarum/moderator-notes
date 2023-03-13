import app from 'flarum/forum/app';
import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import username from 'flarum/common/helpers/username';
import stream from 'flarum/common/utils/Stream';
import withAttr from 'flarum/common/utils/withAttr';

export default class ModeratorNotesCreate extends Modal {
  oninit(vnode) {
    super.oninit(vnode);

    this.noteContent = stream('');
  }

  className() {
    return 'ModeratorNotesCreateModal Modal--large';
  }

  title() {
    return app.translator.trans('fof-moderator-notes.forum.moderatorNotes.create-heading');
  }

  content() {
    return (
      <div className="Modal-body">
        <div className="Form Form--centered">
          <div className="Form-group">
            <div>
              <label>
                {app.translator.trans('fof-moderator-notes.forum.moderatorNotes.input_heading', {
                  username: username(this.attrs.user),
                })}
                <textarea className="FormControl" value={this.noteContent()} oninput={withAttr('value', this.noteContent)} rows="6" />
              </label>
            </div>
          </div>
          <div className="Form-group">
            <Button className="Button Button--primary Button--block" type="submit" loading={this.loading}>
              {app.translator.trans('fof-moderator-notes.forum.moderatorNotes.submit_button')}
            </Button>
          </div>
        </div>
      </div>
    );
  }

  onsubmit(e) {
    e.preventDefault();

    this.loading = true;

    app.store
      .createRecord('moderatorNote')
      .save(
        {
          userId: this.attrs.user.id(),
          note: this.noteContent(),
        },
        { errorHandler: this.onerror.bind(this) }
      )
      .then(this.hide.bind(this))
      .then(this.attrs.callback)
      .catch(() => {})
      .then(this.loaded.bind(this));
  }

  onerror(error) {
    if (error.status === 422) {
      error.alert.attrs = app.translator.trans('fof-moderator-notes.forum.no_content_given');
    }

    super.onerror(error);
  }
}
