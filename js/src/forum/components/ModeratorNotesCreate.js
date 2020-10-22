import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';
import username from 'flarum/helpers/username';
import stream from 'flarum/utils/Stream';
import withAttr from 'flarum/utils/withAttr';

export default class ModeratorNotesCreate extends Modal {
    oninit(vdom) {
        super.oninit(vdom);

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
                                <textarea
                                    className="FormControl"
                                    value={this.noteContent()}
                                    oninput={withAttr('value', this.noteContent)}
                                    rows="6"
                                />
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
            .createRecord('notes')
            .save(
                {
                    userId: this.attrs.user.id(),
                    note: this.noteContent(),
                },
                { errorHandler: this.onerror.bind(this) }
            )
            .then(this.hide.bind(this))
            .then(this.attrs.callback)
            .catch(() => {});
    }

    onerror(error) {
        if (error.status === 422) {
            error.alert.attrs.children = app.translator.trans('fof-moderator-notes.forum.moderatorNotes.no_content_given');
        }

        super.onerror(error);
    }
}
