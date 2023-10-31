import IBase from "../models/users/IBase";
import { ICategory } from "../models/ICategory";

interface IGroupCategory extends IBase {
    cat_id: number,
    name: string,
    category: ICategory,
    image: string,
    link: string,
    is_top: string,
}
export {
    IGroupCategory,
}