import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';
import username from 'flarum/helpers/username';

export default class ModeratorNotesCreate extends Modal {
    init() {
        super.init();

        this.noteContent = m.prop('');
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
                                    username: username(this.props.user),
                                })}
                                <textarea
                                    className="FormControl"
                                    value={this.noteContent()}
                                    oninput={m.withAttr('value', this.noteContent)}
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
                    userId: this.props.user.id(),
                    note: this.noteContent(),
                },
                { errorHandler: this.onerror.bind(this) }
            )
            .then(this.hide.bind(this))
            .then(this.props.callback)
            .catch(() => {});
    }

    onerror(error) {
        if (error.status === 422) {
            error.alert.props.children = app.translator.trans('fof-moderator-notes.forum.moderatorNotes.no_content_given');
        }

        super.onerror(error);
    }
}
