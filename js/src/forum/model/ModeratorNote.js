import Model from 'flarum/common/Model';

export default class ModeratorNote extends Model {
  id = Model.attribute('id');
  note = Model.attribute('note');
  createdAt = Model.attribute('createdAt', Model.transformDate);
  addedByUser = Model.hasOne('addedByUser');
}
