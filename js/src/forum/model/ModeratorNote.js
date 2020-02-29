import Model from 'flarum/Model';

export default class ModeratorNote extends Model {
    id = Model.attribute('id');
    note = Model.attribute('note');
    createdAt = Model.attribute('createdAt', Model.transformDate);
    addedByUser = Model.hasOne('addedByUser');
}
