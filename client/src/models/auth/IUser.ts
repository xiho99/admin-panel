interface IUser {
    id: number,
    name: string,
    email: string,
    email_verified_at: string,
    created_at: string,
    updated_at: string,
    role: string,
}
interface IUserResponse {
    Users: IUser[],
}

export {
    IUser,
    IUserResponse,
};
