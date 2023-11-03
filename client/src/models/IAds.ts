import IBase from "../models/users/IBase";

interface IAds extends IBase {
    title: string,
    link: string,
    image: string | undefined,
}
export type { IAds }