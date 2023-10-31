import IBase from "../models/users/IBase";

interface IMenu extends IBase {
    name: string,
    image: string,
    desc: string | null,
    link: string,
    type: string,
    color: string | null,
}
export {
    IMenu,
}