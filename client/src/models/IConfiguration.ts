import IBase from "../models/users/IBase";

interface IConfiguration extends IBase{
    id: number,
    appName: string,
    key: string,
    input: string,
    value: string,
    sort: number,
    type: string,
}
export type {
    IConfiguration
}