import IBase from "../models/users/IBase";

interface IMenu extends IBase {
    name: string,
    image: string,
    link: string,
    type: string,
    color: string | null,
}
export type {
    IMenu,
}