import IBase from "../models/users/IBase";

interface IMoreSite extends IBase {
    name: string,
    image: string | null,
    link: string,
}
export {
    IMoreSite,
}