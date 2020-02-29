import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';

export default class ModeratorNotesCreate extends Modal {
    init() {
        super.init();

        this.success = false;

        this.noteContent = m.prop('');

        this.user = this.props.user;
    }

    className() {
        return 'ModeratorNotesCreateModal Modal--large';
    }

    title() {
        return app.translator.trans('fof-moderator-notes.forum.moderatorNotes.create-heading');
    }

    content() {
        if (this.success) {
            return (
                <div className="Modal-body">
                    <div className="Form Form--centered">
                        <p>{app.translator.trans('fof-moderator-notes.forum.moderatorNotes.confirmation_message')}</p>
                        <div className="Form-group">
                            <Button className="Button Button--primary Button--block" onclick={this.hide.bind(this)}>
                                {app.translator.trans('fof-moderator-notes.forum.moderatorNotes.dismiss_button')}
                            </Button>
                        </div>
                    </div>
                </div>
            );
        }

        return (
            <div className="Modal-body">
                <div className="Form Form--centered">
                    <div className="Form-group">
                        <div>
                            <label>
                                {app.translator.trans('fof-moderator-notes.forum.moderatorNotes.input_heading', {
                                    username: this.user.username(),
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
                    userId: this.user.id(),
                    note: this.noteContent(),
                },
                { errorHandler: this.onerror.bind(this) }
            )
            .then(() => (this.success = true))
            .catch(() => {})
            .then(this.loaded.bind(this));
    }

    onerror(error) {
        if (error.status === 422) {
            error.alert.props.children = app.translator.trans('fof-moderator-notes.forum.moderatorNotes.no_content_given');
        }

        super.onerror(error);
    }
}
