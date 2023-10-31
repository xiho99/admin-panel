interface IRegisterRequest {
    name: string,
    email: string,
    password: string,
    password_confirmation: string,
}
interface IRegisterResponse {
  id: number,
  token: string,
  username: string,
  email: string,
  email_verified_at: string,
  created_at: number,
  updated_at: number,
}
interface IRegisterResponse {
    Authorization: IRegisterRequest,
    Message: string,
}

export {
  IRegisterRequest,
  IRegisterResponse,
};
