import IBase from "../models/users/IBase";

interface IConfiguration extends IBase{
    configuration_name: string,
    key: string,
    input: string,
    content: string,
}
export {
    IConfiguration,
}