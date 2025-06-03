import { type RouteConfig, index, route } from "@react-router/dev/routes";

export default [
  index("routes/home.tsx"),
  route("/offers", "routes/offers.tsx"),
  route("/offers/:id", "routes/offers.$id.tsx"),
  route("/payment/:bookingId", "routes/payment.$bookingId.tsx"),
] satisfies RouteConfig;
