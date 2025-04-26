export default function save({ attributes }) {
  const { profileData } = attributes;

  return (
    <section>
      <p>{profileData.title}</p>
    </section>
  );
}
