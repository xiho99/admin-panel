import IBase from "../models/users/IBase";
import functionHelper from "/@/libraries/formatHelpers/functionHelper";

interface IGroup extends IBase{
    cat_id: number,
    name: string,
    image: string,
    link: string,
}
interface IGroupCategory extends IBase{
    cat_id: number,
    name: string,
    key: string,
    group: IGroup[],
}
class Group implements IGroup {
    id: number;
    cat_id: number;
    image: string;
    link: string;
    name: string;
    sort: number;
    is_delete: boolean;
    is_visible: number;
    created_at: string;
    updated_at: string;
    constructor(init: IGroup) {
        this.id = init.id;
        this.cat_id = init.cat_id;
        this.name = init.name;
        this.image = init.image;
        this.link = init.link;
        this.sort = init.sort;
        this.is_visible = init.is_visible;
        this.created_at = functionHelper.dateStringTo12HourWithTime(init.created_at)
        this.updated_at = functionHelper.dateStringTo12HourWithTime(init.updated_at);
    }

}
class GroupCategory implements IGroupCategory {
    id: number;
    cat_id: number;
    key: string;
    name: string;
    group: IGroup[];
    sort: number;
    is_delete: boolean;
    is_visible: number;
    created_at: string;
    updated_at: string;
    constructor(init: IGroupCategory) {
        Object.assign(this, init);
        this.created_at = functionHelper.dateStringTo12HourWithTime(init.created_at)
        this.updated_at = functionHelper.dateStringTo12HourWithTime(init.updated_at);
        this.group = init.group.map((item: IGroup) => new Group(item))
    }

}
export {
    type IGroupCategory,
    type IGroup,
    GroupCategory,
}