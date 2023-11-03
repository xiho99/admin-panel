import IBase from "../models/users/IBase";

interface ICategory extends IBase{
    id: number,
    name: string,
    slug: string | null,
}
export type {
    ICategory,
}